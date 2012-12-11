<?php
SiteTree::add_extension('Page', 'PageWidgetDecorator');
SortableDataObject::add_sortable_classes(array(
	'LinkListItem', 'MultiTeaserBlockItem', 'MultiTeaserImageBlockItem',
	'CarouselWidgetItem'
));
