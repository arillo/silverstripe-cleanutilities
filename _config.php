<?php
DataObject::add_extension("SiteTree", "UtilitiesDataExtension");

DataObject::add_extension("CleanFile", "SortableDataExtension");
DataObject::add_extension("CleanImage", "SortableDataExtension");
DataObject::add_extension("CleanVideo", "SortableDataExtension");
DataObject::add_extension("CleanLink", "SortableDataExtension");
DataObject::add_extension("CleanTeaser", "SortableDataExtension");

DataObject::add_extension("CleanFile", "ControlledFolderDataExtension");
DataObject::add_extension("CleanImage", "ControlledFolderDataExtension");
DataObject::add_extension("CleanVideo", "ControlledFolderDataExtension");
DataObject::add_extension("CleanTeaser", "ControlledFolderDataExtension");

