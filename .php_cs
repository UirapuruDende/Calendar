<?php

return Symfony\CS\Config\Config::create()
//    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers([
        'psr0',
        'encoding',
        'short_tag',
        'braces',
        'elseif',
        'function_call_space',
        'function_declaration',
        'indentation',
        'line_after_namespace',
        'linefeed',
        'lowercase_constants',
        'lowercase_keywords',
        'method_argument_space',
        'multiple_use',
        'eof_ending',
        'short_array_syntax',
        'ordered_use',
        'no_blank_lines_before_namespace',
        'newline_after_open_tag',
        'multiline_spaces_before_semicolon',
        'align_double_arrow',
        'parenthesis',
        'php_closing_tag',
        'single_line_after_imports',
        'trailing_spaces',
        'visibility',
    'alias_functions',
    'blankline_after_open_tag',
    'concat_without_spaces',
    'double_arrow_multiline_whitespaces',
    'duplicate_semicolon',
    'empty_return',
    'extra_empty_lines',
    'include',
    'list_commas',
    'multiline_array_trailing_comma',
    'namespace_no_leading_whitespace',
    'new_with_braces',
    'no_blank_lines_after_class_opening',
    'no_empty_lines_after_phpdocs',
    'object_operator',
    'operators_spaces',
//    'phpdoc_align',
//    'phpdoc_indent',
//    'phpdoc_inline_tag',
//    'phpdoc_no_access',
//    'phpdoc_no_empty_return',
//    'phpdoc_no_package',
//    'phpdoc_scalar',
//    'phpdoc_separation',
//    'phpdoc_short_description',
//    'phpdoc_to_comment',
//    'phpdoc_trim',
//    'phpdoc_type_to_var',
//    'phpdoc_var_without_name',
//    'pre_increment',
    'remove_leading_slash_use',
    'remove_lines_between_uses',
    'return',
    'self_accessor',
    'single_array_no_trailing_comma',
    'single_blank_line_before_namespace',
    'single_quote',
    'spaces_before_semicolon',
    'spaces_cast',
    'standardize_not_equal',
    'ternary_spaces',
    'trim_array_spaces',
//    'unalign_double_arrow',
//    'unalign_equals',
    'unary_operators_spaces',
    'unused_use',
    'whitespacy_lines',
    'align_double_arrow',
//    'align_equals',
    'concat_with_spaces',
//    'ereg_to_preg',
//    'header_comment',
//    'long_array_syntax',
    'multiline_spaces_before_semicolon',
    'newline_after_open_tag',
    'no_blank_lines_before_namespace',
    'ordered_use',
//    'php4_constructor',
    'phpdoc_order',
//    'phpdoc_var_to_type',
    'short_array_syntax',
//    'short_echo_tag',
//    'strict',
//    'strict_param',
    ])
    ->setUsingCache(true)
    ->finder(Symfony\CS\Finder\DefaultFinder::create()->in(__DIR__."/"));
