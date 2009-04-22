<?php
//
// SOFTWARE NAME: eZ Object States Filter
// SOFTWARE RELEASE: 1.0
// COPYRIGHT NOTICE: Copyright (C) 2009 Damien POBEL
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//


class eZObjectStatesFilter
{

    /**
     * Return the array needed by the extended attribute filter system
     *
     * @param array $params : needs to have a 'states_identifiers' key
     * containing an array of states identifiers to filter on. States
     * identifiers are string like "state_group_identifier/state_identifier". It
     * can also have a 'operator' key that can be 'and' or 'or' string.
     *
     * @return array
     */
    static function createSQLParts( $params )
    {
        eZDebug::writeDebug( $params, __METHOD__ );
        $result = array( 'tables'  => false,
                         'columns' => false,
                         'joins'   => false );
        $operator = ' AND ';
        if ( isset( $params['operator'] )
                && ( strcasecmp( $params['operator'], 'and' ) === 0
                    || strcasecmp( $params['operator'], 'or' ) === 0 ) )
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
        else
        {
            eZDebug::writeError( 'No states identifiers given to filter on', __METHOD__ );
        }
        return $result;
    }

}






?>
