#Arillo CleanUtlities
Arillo CleanUtilities is a module for Silverstripe 3.1 which provides functionality that makes the daily development easier and faster. It comes with a couple of classes which can be easily added as extensions to the SiteTree class.

It also gives more functionality to several other sapphire classes. For more information, please consult the docs and the example sections.
##Installation
As with all Silverstripe modules, you have to copy cleanutilities folder and all [dependent modules](#Dependencies) into your project's root directory. After that you should run dev/build and you are done. You should ensure, that you name the module folder "cleanutilities". If you have to, for some reason, use an other name than the standard one you can set it in _config.php like:

	CleanUtils::$module = "renamed_cleanutilities";
	
This is important because some classes use the value of this variable for loading dependent files for example.
##Included packages
* [Clean Actions](Clean_Actions.md)
* [Clean Extensions](Clean_Extensions.md)
* [Clean Forms](Clean_Forms.md)
* [Clean Models](Clean_Models.md)
* [Model Decorators](Model_Decorators.md)
* [Clean Utils](Clean_Utils.md)
* [Utilities Decorators](Utilities_Decorators.md)


##Dependencies
CleanUtilities is dependend on following third-party modules:

* [GridFieldBulkEditingTools](https://github.com/colymba/GridFieldBulkEditingTools)
* [GridFieldRelationHandler](https://github.com/simonwelsh/silverstripe-GridFieldRelationHandler)
* [SortableGridField](https://github.com/UndefinedOffset/SortableGridField)

---
Brought to you by [Arillo](http://arillo.net).