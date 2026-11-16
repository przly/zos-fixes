<?php

function getDetailModuleFields(int $id, int $k): void
{
	$elementsArray = [];
	$flexibleContentElements = getPageBuilderFields($id, $k, 'elements');

	foreach ($flexibleContentElements as $key => $value) {
		$prefix = "elements_{$key}_";

		$result = match ($value) {
			'getEditorTextFields' => getEditorTextFieldsArray($id, $k, $prefix),
			'getEmbedVideoFields' => getEmbedVideoFieldsArray($id, $k, $prefix),
			'gallery' => getGalleryFieldsArray($id, $k, $prefix),
			default => null,
		};

		if ($result !== null) {
			$elementsArray[] = $result;
		}
	}

	$postType = get_post_type($id);

	if ('news' === $postType) {
		$tag = getPrimaryCategory($id, 'news-category');
		$tag = $tag?->name;
		$date = get_the_date('Y-m-d', $id);
		if ($dateObject = DateTime::createFromFormat('Y-m-d', $date)) {
			$formatDate = $dateObject->format('d') . ' ' .
				MONTH_NAME[FRONT_LANGUAGE][$dateObject->format('F')] . ' ' .
				$dateObject->format('Y');
		};
	} else {
		$tag = getPrimaryCategory($id, 'target-group');
		$tag = $tag?->name;
		$date = get_post_meta($id, 'event_date_start', true);
		if ($dateObject = DateTime::createFromFormat('Ymd', $date)) {
			$formatDate = $dateObject->format('d') . ' ' .
				MONTH_NAME[FRONT_LANGUAGE][$dateObject->format('F')] . ' ' .
				$dateObject->format('Y');
		};
		if ($time = get_post_meta($id, 'event_time_start', true)) {
			$formatTime = date('H:i', strtotime($time));
		}
	}

	$imageID = get_post_thumbnail_id($id);

	Twig::renderWithAttrs(
		'/03-modules/detail-module/detail-module.twig',
		[
			'detail_module' => [
				'date__date' => $formatDate ?? '',
				'text__time' => $formatTime ?? '',
				'text__tag' => $tag ?? '',
				'text__title' => get_the_title($id),
				'image__featured_image' => getImgUrlFromImageID($imageID),
				'featured_image_alt' => getImgAltFromImageID($imageID),
				...\User\UserHelper::protectedContent($elementsArray)
			],
		],
		$id,
		$k
	);
}

function getGalleryFieldsArray(int $id, int $k, string $key): array
{
	$galleryItems = getPageBuilderFields($id, $k, $key . 'gallery');

	$galleryArray = [];
	foreach ($galleryItems as $imageID) {
		$galleryArray[] = [
			'image__image' => getImgUrlFromImageID($imageID),
			'image_alt' => getImgAltFromImageID($imageID)
		];
	}

	return [
		'repeater__images' => $galleryArray
	];
}
