<?php

/* DECORATORS */
Object::add_extension('SiteTree', 'Translatable');

Object::add_extension('SiteTree','UtilitiesDecorator');

Object::add_extension('DataObjectSet', 'DataObjectSetDecorator');

Object::add_extension('SiteConfig', 'SiteConfigDecorator');

Object::add_extension('SiteTree','AssetsDecorator');
Object::add_extension('CleanImage','AssetsDecorator');
Object::add_extension('CleanFile','AssetsDecorator');
Object::add_extension('CleanTeaser','AssetsDecorator');
Object::add_extension('HTMLText','TextDecorator');

/* SORTABLES */
SortableDataObject::add_sortable_class('CleanImage');
SortableDataObject::add_sortable_class('CleanFile');
SortableDataObject::add_sortable_class('CleanLink');
SortableDataObject::add_sortable_class('CleanTeaser');
SortableDataObject::add_sortable_class('CleanTeaserLink');


Requirements::set_write_js_to_body(false);

SiteTree::enable_nested_urls();

AssetsDecorator::$maxfilesperfolder = 200;