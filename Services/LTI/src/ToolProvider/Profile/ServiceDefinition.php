<?php

namespace ILIAS\LTI\ToolProvider\Profile;

/******************************************************************************
 *
 * This file is part of ILIAS, a powerful learning management system.
 *
 * ILIAS is licensed with the GPL-3.0, you should have received a copy
 * of said license along with the source code.
 *
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 *      https://www.ilias.de
 *      https://github.com/ILIAS-eLearning
 *
 *****************************************************************************/

/**
 * Class to represent an LTI service object
 *
 * @author  Stephen P Vickers <stephen@spvsoftwareproducts.com>
 * @copyright  SPV Software Products
 * @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3
 */
class ServiceDefinition
{

    /**
     * Media types supported by service.
     *
     * @var array|null $formats
     */
    public ?array $formats = null;

    /**
     * HTTP actions accepted by service.
     *
     * @var array|null $actions
     */
    public ?array $actions = null;

    /**
     * ID of service.
     *
     * @var string|null $id
     */
    public ?string $id = null;

    /**
     * URL for service requests.
     *
     * @var string|null $endpoint
     */
    public ?string $endpoint = null;

    /**
     * Class constructor.
     * @param array       $formats  Array of media types supported by service
     * @param array       $actions  Array of HTTP actions accepted by service
     * @param string|null $id       ID of service (optional)
     * @param string|null $endpoint URL for service requests (optional)
     */
    function __construct(array $formats, array $actions, string $id = null, string $endpoint = null)
    {
        $this->formats = $formats;
        $this->actions = $actions;
        $this->id = $id;
        $this->endpoint = $endpoint;
    }

    /**
     * Set ID.
     * @param string $id ID of service
     */
    function setId(string $id)
    {
        $this->id = $id;
    }

}