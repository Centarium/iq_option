<?php
/**
 * @var string $addNodeAction
 * @var string $indexAction
 * @var string $deleteNodeAction
 * @var string $getSubTreeAction
 * @var string $getLevelAction
 */
?>
<head>
    <link rel="stylesheet" href="/assets/build/vendor.css" />
</head>

<div id="nestedTree" role="tree" type="register"
     data-index_action="<?=$indexAction?>"
     data-add_node_action="<?=$addNodeAction?>"
     data-delete_node_action="<?=$deleteNodeAction?>"
     data-get_subtree_action="<?=$getSubTreeAction?>"
     data-get_level_action="<?=$getLevelAction?>"
></div>

<script src="/assets/build/manifest.js"></script>
<script src="/assets/build/vendor.js"></script>

<script src="/assets/build/js/tree.js"></script>