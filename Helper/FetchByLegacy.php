<?php

namespace Ow\Bundle\AgendaBundle\Helper;

use Symfony\Component\DependencyInjection\ContainerAware;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

class FetchByLegacy extends ContainerAware {

    /**
     * @var \Closure
     */
    private $legacyKernelClosure;

    /**
     * @var array
     */
    private $criterion = array();

    /**
     * @var array
     */
    private $fetchParams = array();

    /**
     * @var string
     */
    private $fetchModule = 'content';

    /**
     * @var string
     */
    private $fetchFunction;

    public function fetchContent( $criterion ) {
        $this->fetchModule = 'content';
        $this->fetchFunction = 'object';
        return $this->setCriterion( $criterion )->performFetch();
    }

    public function fetchNode( $criterion ) {
        $this->fetchModule = 'content';
        $this->fetchFunction = 'node';
        return $this->setCriterion( $criterion )->performFetch();
    }

    public function fetchNodeList( $criterion ) {
        $this->fetchModule = 'content';
        $this->fetchFunction = 'list';
        return $this->setCriterion( $criterion )->performFetch();
    }

    public function countNodeList( $criterion ) {
        $this->fetchModule = 'content';
        $this->fetchFunction = 'list_count';
        return (int) $this->setCriterion( $criterion )->performFetch();
    }

    public function fetchNodeTree( $criterion ) {
        $this->fetchModule = 'content';
        $this->fetchFunction = 'tree';
        return $this->setCriterion( $criterion )->performFetch();
    }

    public function countNodeTree( $criterion ) {
        $this->fetchModule = 'content';
        $this->fetchFunction = 'tree_count';
        return (int) $this->setCriterion( $criterion )->performFetch();
    }

    public function fetchObjectState( $criterion ) {
        if( isset( $criterion['ObjectStateIdentifier'] ) ) {
            list( $stateGroupIdentifier, $stateIdentifier ) = explode( '/', $criterion['ObjectStateIdentifier'] );
            return $this->getLegacyKernel()->runCallback(
                    function () use ( $stateGroupIdentifier, $stateIdentifier ) {
                    $objectStateGroup = \eZContentObjectStateGroup::fetchByIdentifier( $stateGroupIdentifier );
                    return $state = $objectStateGroup->stateByIdentifier( $stateIdentifier );
                } );
        }
        if( isset( $criterion['ObjectStateId'] ) ) {
            $stateId = $criterion['ObjectStateId'];
            return $this->getLegacyKernel()->runCallback(
                    function () use ( $stateId ) {
                    return \eZContentObjectState::fetchById( $stateId );
                } );
        }
    }

    public function fetchMoreLikeThis( $criterion ) {
        $this->fetchModule = 'ezfind';
        $this->fetchFunction = 'moreLikeThis';
        return $this->setCriterion( $criterion )->performFetch();
    }

    protected function performFetch() {
        $fetchModule = $this->fetchModule;
        $fetchFunction = $this->fetchFunction;
        $fetchParams = $this->fetchParams;
        return $this->getLegacyKernel()->runCallback(
            function () use ( $fetchModule, $fetchFunction, $fetchParams ) {
                return \eZFunctionHandler::execute( $fetchModule, $fetchFunction, $fetchParams );
        } );
    }

    protected function transformCriterionInFetchParams() {
        $this->fetchParams = array();
        foreach( $this->criterion as $paramName => $value ) {
            $paramName = $this->fromCamelCaseToUnderscores( $paramName );
            switch( $paramName ) {
                case 'visibility':
                    if( $value == Criterion\Visibility::HIDDEN ) {
                        $this->fetchParams['ignore_visibility'] = true;
                    }
                    break;
                case 'content_type_identifier':
                    if( !is_array( $value ) ) {
                        $value = array( $value );
                    }
                    $this->fetchParams['class_filter_array'] = $value;
                    break;
                case 'content_type_identifier_operator':
                    $this->fetchParams['class_filter_type'] = $value;
                    break;
                case 'object_state_id':
                    $this->fetchParams['attribute_filter'][] = array( 'state', "=", $value );
                    break;
                case 'object_state_identifier':
                    $objectState = $this->fetchObjectState( array( 'ObjectStateIdentifier' => $value ) );
                    if( $objectState ) {
                        $this->fetchParams['attribute_filter'][] = array( 'state', "=", $objectState->attribute( 'id' ) );
                    }
                    break;
                default:
                    $this->fetchParams[$paramName] = $value;
                    break;
            }
        }
        if( isset( $this->fetchParams['class_filter_array'] ) && !isset( $this->fetchParams['class_filter_type'] ) ) {
            $this->fetchParams['class_filter_type'] = 'include';
        }

        if( isset( $this->fetchParams['parent_node_id'] ) && !isset( $this->fetchParams['sort_by'] ) && ( $this->fetchFunction == 'list' || $this->fetchFunction == 'tree') ) {
            $parentNodeId = $this->fetchParams['parent_node_id'];
            $this->fetchParams['sort_by'] = $this->getLegacyKernel()->runCallback(
                function () use ( $parentNodeId ) {
                $parentNode = \eZContentObjectTreeNode::fetch( $parentNodeId );
                return $parentNode->attribute( 'sort_array' );
            } );
        }
    }

    protected function getLegacyKernel() {
        if( !isset( $this->legacyKernelClosure ) ) {
            $this->legacyKernelClosure = $this->container->get( 'ezpublish_legacy.kernel' );
        }

        $legacyKernelClosure = $this->legacyKernelClosure;
        return $legacyKernelClosure();
    }

    /**
     * Return fetch criterion
     * 
     * @return array
     */
    protected function getCriterion() {
        return $this->criterion;
    }

    /**
     * Set fetch criterion
     * 
     * @param array $criterion
     * @return \Ow\Bundle\AgendaBundle\Helper\FetchByLegacy
     */
    protected function setCriterion( $criterion ) {
        $this->criterion = $criterion;
        $this->transformCriterionInFetchParams();
        return $this;
    }

    /**
     * Set fetch criterion
     * 
     * @return \Ow\Bundle\AgendaBundle\Helper\FetchByLegacy
     */
    protected function removeCriterion() {
        $this->criterion = array();
        $this->transformCriterionInFetchParams();
        return $this;
    }

    /**
     * Add a critera in the $criterion
     * 
     * @param string $type
     * @param mixed $value
     * @return \Ow\Bundle\AgendaBundle\Helper\FetchByLegacy
     */
    protected function addCriteria( $type, $value ) {
        $this->criterion[$type] = $value;
        $this->transformCriterionInFetchParams();
        return $this;
    }

    /**
     * @param $str
     * @return mixed
     */
    protected function fromCamelCaseToUnderscores( $str ) {
        $str[0] = strtolower( $str[0] );
        $func = create_function( '$c', 'return "_" . strtolower($c[1]);' );
        return preg_replace_callback( '/([A-Z])/', $func, $str );
    }

}
