<?php

/**
 * This file is part of ILIAS, a powerful learning management system
 * published by ILIAS open source e-Learning e.V.
 * ILIAS is licensed with the GPL-3.0,
 * see https://www.gnu.org/licenses/gpl-3.0.en.html
 * You should have received a copy of said license along with the
 * source code, too.
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 * https://www.ilias.de
 * https://github.com/ILIAS-eLearning
 */

/**
 * Import Parser
 *
 * @author Stefan Meyer <meyer@leifos.com>
 */
abstract class ilContainerReferenceXmlParser extends ilSaxParser
{
    public const MODE_CREATE = 1;
    public const MODE_UPDATE = 2;

    private ?ilContainerReference $ref = null;
    private int $parent_id = 0;
    protected ilLogger $logger;
    protected ilImportMapping $import_mapping;
    protected string $cdata = "";
    protected int $mode = 0;
    
    public function __construct(
        string $a_xml,
        int $a_parent_id = 0
    ) {
        global $DIC;

        parent::__construct(null);

        $this->mode = ilContainerReferenceXmlParser::MODE_CREATE;
        $this->setXMLContent($a_xml);

        $this->logger = $DIC->logger()->exp();
    }

    public function setImportMapping(ilImportMapping $mapping) : void
    {
        $this->import_mapping = $mapping;
    }
    
    public function getParentId() : int
    {
        return $this->parent_id;
    }
    
    public function setHandlers($a_xml_parser)
    {
        xml_set_object($a_xml_parser, $this);
        xml_set_element_handler($a_xml_parser, 'handlerBeginTag', 'handlerEndTag');
        xml_set_character_data_handler($a_xml_parser, 'handlerCharacterData');
    }

    public function startParsing()
    {
        parent::startParsing();
        
        if ($this->ref instanceof ilContainerReference) {
            return $this->ref;
        }
        return 0;
    }


    public function handlerBeginTag(
        $a_xml_parser,
        string $a_name,
        array $a_attribs
    ) : void {
        switch ($a_name) {
            case "ContainerReference":
                break;
            
            case 'Title':
                switch ($a_attribs['type']) {

                    case ilContainerReference::TITLE_TYPE_REUSE:
                    default:
                        $this->getReference()->setTitleType(ilContainerReference::TITLE_TYPE_REUSE);
                        break;
                }
                break;
            
            case 'Target':
                $target_id = $this->parseTargetId($a_attribs['id'] ?? '');
                if ($target_id) {
                    $this->logger->debug('Using mapped target_id: ' . $target_id);
                    $this->getReference()->setTargetId($target_id);
                } else {
                    $this->logger->info('No mapping found for: ' . $a_attribs['id']);
                    $this->getReference()->setTargetId(0);
                }
                break;
        }
    }

    protected function parseTargetId(string $attribute_target) : int
    {
        if (!strlen($attribute_target)) {
            $this->logger->debug('No target id provided');
            return 0;
        }
        if (!$this->import_mapping instanceof ilImportMapping) {
            return 0;
        }
        $obj_mapping_id = $this->import_mapping->getMapping('Services/Container', 'objs', $attribute_target);
        if (!$obj_mapping_id) {
            $this->logger->debug('Cannot find object mapping for target_id: ' . $attribute_target);
            return 0;
        }
        return $obj_mapping_id;
    }


    public function handlerEndTag(
        $a_xml_parser,
        string $a_name
    ) : void {
        switch ($a_name) {
            case "ContainerReference":
                $this->save();
                break;
            
            case 'Title':
                if ($this->getReference()->getTitleType() == ilContainerReference::TITLE_TYPE_CUSTOM) {
                    $this->getReference()->setTitle(trim($this->cdata));
                }
                break;
        }
        $this->cdata = '';
    }

    public function handlerCharacterData(
        $a_xml_parser,
        string $a_data
    ) : void {
        #$a_data = str_replace("<","&lt;",$a_data);
        #$a_data = str_replace(">","&gt;",$a_data);

        if (!empty($a_data)) {
            $this->cdata .= $a_data;
        }
    }

    abstract protected function create() : void;

    protected function save() : void
    {
        if ($this->mode == ilCategoryXmlParser::MODE_CREATE) {
            $this->create();
            $this->getReference()->create();
            $this->getReference()->createReference();
            $this->getReference()->putInTree($this->getParentId());
            $this->getReference()->setPermissions($this->getParentId());
        }
        $this->getReference()->update();
    }

    public function setMode(int $mode) : void
    {
        $this->mode = $mode;
    }

    
    public function setReference(ilContainerReference $ref) : void
    {
        $this->ref = $ref;
    }
    
    public function getReference() : ilContainerReference
    {
        return $this->ref;
    }
}
