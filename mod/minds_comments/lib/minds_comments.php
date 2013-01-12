<?php

function minds_comments_view_list($type, $pid) {
	$limit = 3;

	$mc = new MindsComments();
	$call = $mc -> output($type, $pid, 3, 0);
	$count = $call['hits']['total'];
	$comments = array_reverse($call['hits']['hits']);

	foreach ($comments as $comment) {
		$visible .= minds_comments_view_comment($comment);
	}

	if ($count > 0 && $count > $limit) {
		$remainder = $count - $limit;
		if ($limit > 0) {
			$summary = elgg_echo('hj:alive:comments:remainder', array($remainder));
		} else {
			$summary = elgg_echo('hj:alive:comments:viewall', array($remainder));
		}
	}

	return elgg_view('minds_comments/list', array('summary' => $summary, 'visible' => $visible, 'hidden' => $hidden));
}

function minds_comments_view_comment($comment) {
	$owner = get_entity($comment['_source']['owner_guid']);
	$icon = elgg_view_entity_icon($owner, 'tiny');

	$author = elgg_view('output/url', array('text' => $owner -> name, 'href' => $owner -> getURL(), 'class' => 'minds-comments-owner'));
	
	$menu = elgg_view_menu('comments', array(
		    'type' => $comment['_type'],
		    'pid'=>$comment['_source']['pid'],
			'id'=>$comment['_id'],
		    'handler' => $handler,
		    'class' => 'elgg-menu-hz',
		    'sort_by' => 'priority',
		    'params' => $params
		));
	
	$content .= $menu;
	
	$content .= $author . ': ' . minds_filter($comment['_source']['description']);
	$content .= '<br/><span class="minds-comments-timestamp"' . elgg_view_friendly_time($comment['_source']['time_created']) . '</span>';
	
	return elgg_view_image_block($icon, $content, array('class' => 'minds-comment'));

}

function hj_alive_count_comments($entity, $params) {
	$parent_guid = elgg_extract('parent_guid', $params, null);
	$river_id = elgg_extract('river_id', $params, null);
	$annotation_name = elgg_extract('aname', $params, 'generic_comment');

	$options = array('type' => 'object', 'subtype' => 'hjannotation', 'owner_guid' => null,
	//'container_guid' => $container_guid,
	'metadata_name_value_pairs' => array( array('name' => 'annotation_name', 'value' => $annotation_name), array('name' => 'annotation_value', 'value' => '', 'operand' => '!='), array('name' => 'parent_guid', 'value' => $parent_guid), array('name' => 'river_id', 'value' => $river_id)), 'count' => true, 'limit' => 3, 'order_by' => 'e.time_created desc');

	$count = elgg_get_entities_from_metadata($options);

	return $count;
}