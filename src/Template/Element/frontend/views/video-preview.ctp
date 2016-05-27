<?php
/**
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
 * @copyright	Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 */
use Cake\Routing\Router;
?>

<div class="content-preview">
	<a href="<?= Router::url(['_name' => 'video', $video->id]) ?>">
		<div>
			<div>
				<div class="content-title">
					<?php
						if(isset($truncate['title']) && !$truncate['title']) {
							echo $video->title;
                        }
						else {
							echo $this->Text->truncate($video->title, empty($truncate['title']) ? 40 : $truncate['title'], ['exact' => FALSE]);
                        }
					?>
				</div>
				<?php if(!empty($video->description)): ?>
					<div class="content-text">
						<?php
							if(isset($truncate['description']) && !$truncate['description']) {
								echo strip_tags($video->description);
                            }
							else {
								echo $this->Text->truncate(strip_tags($video->description), empty($truncate['description']) ? 80 : $truncate['description'], ['exact' => FALSE]);
                            }
						?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?= $this->Thumb->image($video->preview, ['side' => 205]) ?>
	</a>
</div>