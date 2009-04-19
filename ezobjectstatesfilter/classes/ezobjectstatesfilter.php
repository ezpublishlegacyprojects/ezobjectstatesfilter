<?php

class eZObjectStatesFilter
{

    static function createSQLParts( $params )
    {
        eZDebug::writeDebug( $params, __METHOD__ );
        $result = array( 'tables'  => false,
                         'columns' => false,
                         'joins'   => false );
        $operator = ' AND ';
        if ( isset( $params['operator'] ) )
        {
            $operator = ' ' . $params['operator'] . ' ';
        }
        if ( isset( $params['states_identifiers'] )
                && is_array( $params['states_identifiers'] ) )
        {
            $db = eZDB::instance();
            $joins = array();
            $tables = array();
            $i = 1;
            foreach( $params['states_identifiers'] as $stateString )
            {
                list( $groupIdentifier, $stateIdentifier ) = explode( '/', $stateString );
                $tables[] = ' ezcobj_state_group sg' . $i . ', ezcobj_state s' . $i . ', ezcobj_state_link sl' . $i;
                $joins[]  = ' ( sg' . $i . '.identifier="' . $db->escapeString( $groupIdentifier ) . '"
                             AND sg' . $i . '.id=s' . $i . '.group_id
                             AND s' . $i . '.identifier="' . $db->escapeString( $stateIdentifier ) . '"
                             AND sl' . $i . '.contentobject_state_id=s' . $i . '.id
                             AND sl' . $i . '.contentobject_id=ezcontentobject.id ) ';
                $i++;
            }
            $result['tables'] = ',' . implode( ',', $tables );
            $result['joins']  = ' ( ' . implode( $operator, $joins ) . ' ) AND ';
        }
        return $result;
    }

}






?>
