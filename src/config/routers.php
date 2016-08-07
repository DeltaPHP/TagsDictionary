<?php

return [
    //dictionary
    "tags_dictionary_list" => ["/admin/tags_dictionary", ["adminDictionary", "list"]],
    "tags_dictionary_add" => ["/admin/tags_dictionary/add", ["adminDictionary", "form"]],
    "tags_dictionary_edit" =>
        [
            "methods" => [\DeltaRouter\Route::METHOD_GET],
            "patterns" => [
                "type" => \DeltaRouter\RoutePattern::TYPE_REGEXP,
                "value" => "^/admin/tags_dictionary/edit/(?P<id>\w+)$",
            ],
            "action" => ["adminDictionary", "form"],
        ],
    "tags_dictionary_save" =>
        [
            "methods" => [\DeltaRouter\Route::METHOD_POST],
            "patterns" => [
                "value" => "/admin/tags_dictionary/save",
            ],
            "action" => ["adminDictionary", "save"],
        ],
    "tags_dictionary_rm" =>
        [
            "methods" => [\DeltaRouter\Route::METHOD_GET],
            "patterns" => [
                "type" => \DeltaRouter\RoutePattern::TYPE_REGEXP,
                "value" => "^/admin/tags_dictionary/rm/(?P<id>\w+)$",
            ],
            "action" => ["adminDictionary", "rm"],
        ],

    //tags
    "tags_list" =>
        [
            "methods" => [\DeltaRouter\Route::METHOD_GET],
            "patterns" => [
                "type" => \DeltaRouter\RoutePattern::TYPE_REGEXP,
                "value" => "^/admin/tags/?(?P<dictionary>[[:alnum:]]+)?$",
            ],
            "action" => ["adminTag", "list"],
        ],
    "tags_add" => ["/admin/tags/add", ["adminTag", "form"]],
    "tags_edit" =>
        [
            "methods" => [\DeltaRouter\Route::METHOD_GET],
            "patterns" => [
                "type" => \DeltaRouter\RoutePattern::TYPE_REGEXP,
                "value" => "^/admin/tags/edit/(?P<id>\w+)$",
            ],
            "action" => ["adminTag", "form"],
        ],
    "tags_save" =>
        [
            "methods" => [\DeltaRouter\Route::METHOD_POST],
            "patterns" => [
                "value" => "/admin/tags/save",
            ],
            "action" => ["adminTag", "save"],
        ],
    "tags_rm" =>
        [
            "methods" => [\DeltaRouter\Route::METHOD_GET],
            "patterns" => [
                "type" => \DeltaRouter\RoutePattern::TYPE_REGEXP,
                "value" => "^/admin/tags/rm/(?P<id>\w+)$",
            ],
            "action" => ["adminTag", "rm"],
        ],
];
