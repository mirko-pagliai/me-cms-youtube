## Compatibilità
L'ultima versione di MeYoutube è compatibile con [MeCms](http://github.com/mirko-pagliai/MeCms) 1.2.1.

## RSS video
Di default, MeCms fornisce soltanto gli articoli tramite RSS. Per fornire articoli e video, modificare il proprio layout così:

	echo $this->Html->meta(__d('me_cms', 'Latest posts'), '/posts/rss', array('type' => 'rss'));
	echo $this->Html->meta(__d('me_youtube', 'Latest videos'), '/videos/rss', array('type' => 'rss'));