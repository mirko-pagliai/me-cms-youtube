<?php
/**
 * YoutubeVideosController.
 *
 * This file is part of MeYoutube.
 *
 * MeYoutube is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeYoutube is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeYoutube.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2014, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeYoutube\Controller
 */

App::uses('MeCmsAppController', 'MeCms.Controller');

/**
 * YoutubeVideos Controller
 */
class YoutubeVideosController extends MeCmsAppController {
	/**
	 * Parses a YouTube video and returns the video ID
	 * @param string $url Video url
	 * @return mixed Video ID or FALSE
	 */
	protected function _parseUrl($url) {
		//Parses the url
		$url = parse_url($url);
		
		//Checks if it's a Youtube address
		if(empty($url['host']) || !preg_match('/youtube\.com$/', $url['host'])) {
			$this->Session->flash(__d('me_youtube', 'This is not a %s video', 'YouTube'), 'error');
			return FALSE;
		}

		//Checks if it's a valid query address
		if(empty($url['query'])) {
			$this->Session->flash(__d('me_youtube', 'The video address is incorrect'), 'error');
			return FALSE;
		}

		$query = array();

		foreach(explode('&', $url['query']) as $string) {
			$exploded = explode('=', $string);
			$query[$exploded[0]] = $exploded[1];
		}

		//Checks if the video ID is present
		if(empty($query['v'])) {
			$this->Session->flash(__d('me_youtube', 'The video ID is not present'), 'error');
			return FALSE;
		}

		return $query['v'];
	}

	/**
	 * List youtube videos
	 */
	public function admin_index() {
		$this->paginate = array(
			'contain'	=> 'User.username',
			'fields'	=> array('id', 'youtube_id', 'title', 'priority', 'active', 'is_spot', 'created'),
			'limit'		=> $this->config['records_for_page']
		);
				
		$this->set(array(
			'videos'			=> $this->paginate(),
			'title_for_layout'	=> __d('me_youtube', 'Videos')
		));
	}

	/**
	 * Add video
	 */
	public function admin_add() {
		if(!empty($this->request->query['url'])) {
			$youtubeId = $this->_parseUrl($this->request->query['url']);
			$this->set(compact('youtubeId'));
		}
		
		if($this->request->is('post')) {
			$this->YoutubeVideo->create();
			if($this->YoutubeVideo->save($this->request->data)) {
				$this->Session->flash(__d('me_youtube', 'The video has been created'));
				$this->redirect(array('action' => 'index'));
			}
			else
				$this->Session->flash(__d('me_youtube', 'The video could not be created. Please, try again'), 'error');
		}

		$this->set(array(
			'users'				=> $this->YoutubeVideo->User->find('list'),
			'title_for_layout'	=> __d('me_youtube', 'Add video')
		));
	}

	/**
	 * Edit video
	 * @param string $id Video ID
	 */
	public function admin_edit($id = NULL) {
		if(!$this->YoutubeVideo->exists($id))
			throw new NotFoundException(__d('me_youtube', 'Invalid video'));
			
		if($this->request->is('post') || $this->request->is('put')) {
			if($this->YoutubeVideo->save($this->request->data)) {
				$this->Session->flash(__d('me_youtube', 'The video has been edited'));
				$this->redirect(array('action' => 'index'));
			}
			else
				$this->Session->flash(__d('me_youtube', 'The video could not be edited. Please, try again'), 'error');
		} 
		else
			$this->request->data = $this->YoutubeVideo->find('first', array(
				'conditions'	=> array('id' => $id),
				'fields'		=> array('id', 'user_id', 'title', 'subtitle', 'youtube_id', 'description', 'priority', 'active', 'is_spot', 'created')
			));

		$this->set(array(
			'users'				=> $this->YoutubeVideo->User->find('list'),
			'title_for_layout'	=> __d('me_youtube', 'Edit video')
		));
	}

	/**
	 * Delete video
	 * @param string $id Video ID
	 * @throws NotFoundException
	 */
	public function admin_delete($id = NULL) {
		$this->YoutubeVideo->id = $id;
		if(!$this->YoutubeVideo->exists())
			throw new NotFoundException(__d('me_youtube', 'Invalid video'));
			
		$this->request->onlyAllow('post', 'delete');
		
		if($this->YoutubeVideo->delete())
			$this->Session->flash(__d('me_youtube', 'The video has been deleted'));
		else
			$this->Session->flash(__d('me_youtube', 'The video was not deleted'), 'error');
			
		$this->redirect(array('action' => 'index'));
	}
}