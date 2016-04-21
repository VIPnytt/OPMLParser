<?php
namespace vipnytt\OPMLParser;

/**
 * Interface OPMLInterface
 *
 * @package vipnytt\OPMLParser
 */
interface OPMLInterface
{
    /**
     * Encoding to use if not provided
     */
    const ENCODING = 'UTF-8';

    /**
     * OPML versions supported
     */
    const SUPPORTED_VERSIONS = [
        '2.0',
        '1.0'
    ];

    /**
     * Default OPML version to use, if none is set
     */
    const VERSION_DEFAULT = '2.0';

    /**
     * Optional <head> elements
     */
    const OPTIONAL_HEAD_ELEMENTS = [
        'title',
        'dateCreated',
        'dateModified',
        'ownerName',
        'ownerEmail',
        'ownerId',
        'docs',
        'expansionState',
        'vertScrollState',
        'windowTop',
        'windowLeft',
        'windowBottom',
        'windowRight'
    ];
}
