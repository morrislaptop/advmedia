<?php
/**
 * Attachments Element File
 *
 * Element listing associated attachments of the view's model
 * Add, delete (detach) an Attachment
 *
 * Copyright (c) 2007-2009 David Persson
 *
 * Distributed under the terms of the MIT License.
 * Redistributions of files must retain the above copyright notice.
 *
 * PHP version 5
 * CakePHP version 1.2
 *
 * @package    media
 * @subpackage media.views.elements
 * @copyright  2007-2009 David Persson <davidpersson@gmx.de>
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link       http://github.com/davidpersson/media
 */

if (!isset($previewVersion)) {
	$previewVersion = 'xxs';
}

/* Set $assocAlias and $model if you're using this element multiple times in one form */

if (!isset($assocAlias)) {
	$assocAlias = 'Attachment';
} else {
	$assocAlias = Inflector::singularize($assocAlias);
}

if (!isset($model)) {
	$model = $form->model();
}

$modelId = $form->value($form->model().'.id');

if (isset($this->data[$assocAlias][0]['basename'])) {
	array_unshift($this->data[$assocAlias],array());
}
?>
<div class="attachments element">
	<?php printf(__('%s', true), $assocAlias) ?>
	<!-- New Attachment -->
	<div class="new">
	<?php
		echo $form->hidden($assocAlias . '.model', array('value' => $model));
		echo $form->hidden($assocAlias . '.group', array('value' => strtolower($assocAlias)));
		echo $form->input($assocAlias . '.file', array(
			'label' => __('File', true),
			'type'  => 'file',
			'error' => array(
				'error'      => __('An error occured while transferring the file.', true),
				'resource'   => __('The file is invalid.', true),
				'access'     => __('The file cannot be processed.', true),
				'location'   => __('The file cannot be transferred from or to location.', true),
				'permission' => __('Executable files cannot be uploaded.', true),
				'size'       => __('The file is too large.', true),
				'pixels'     => __('The file is too large.', true),
				'extension'  => __('The file has wrong extension.', true),
				'mimeType'   => __('The file has wrong mime type.', true),
		)));
		echo $form->input($assocAlias . '.alternative', array(
			'label' => __('Textual replacement', true),
			'value' => '',
			'error' => __('A textual replacement must be provided.', true)
		));
	?>
	</div>
	<!-- Existing Attachments -->
	<div class="existing"
	<?php if ( !empty($this->data[$assocAlias]['id']) ): ?>
		<div>
		<?php
			$item = $this->data[$assocAlias];

			echo $form->hidden($assocAlias . '.id', array('value' => $item['id']));
			echo $form->hidden($assocAlias . '.model', array('value' => $model));
			echo $form->hidden($assocAlias . '.group', array('value' => $item['group']));
			echo $form->hidden($assocAlias . '.dirname', array('value' => $item['dirname']));
			echo $form->hidden($assocAlias . '.basename', array('value' => $item['basename']));
			echo $form->hidden($assocAlias . '.alternative', array('value' => $item['alternative']));

			if ($file = $medium->file($item)) {
				$url = $medium->url($file);

				echo $medium->embed($medium->file($previewVersion . '/', $item), array(
					'restrict' => array('image')
				));

		 		$Medium = Medium::factory($file);
				$size = $medium->size($file);

				if (isset($number)) {
					$size = $number->toReadableSize($size);
				} else {
					$size .= ' Bytes';
				}

				printf('<span>%s&nbsp;(%s/%s) <em>%s</em></span>',
						$url ? $html->link($item['basename'], $url) : $item['basename'],
						strtolower($Medium->name), $size, $item['alternative']);
			}

			echo $form->input($assocAlias . '.delete', array(
				'label' => __('Release', true),
				'type' => 'checkbox',
				'value' => 0
			));
		?>
		</div>
	<?php endif ?>
	</div>
</div>