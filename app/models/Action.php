<?php

namespace models;

class Action {

	private $db;
	
	public function __construct()
	{
		\F3::set('db', new \DB\SQL(
            'mysql:host=' . \F3::get('db_host') . ';port=' . \F3::get('db_port') . ';dbname='.\F3::get('db_database'),
            \F3::get('db_username'),
            \F3::get('db_password')
        ));
	}

	public function getNextActionsByContext() {
		$sql = 'SELECT it.itemId AS actionId,
					   co.name AS contextName,
					   ti.timeframe,
					   it.title,
					   GROUP_CONCAT(DISTINCT it2.title ORDER BY it2.title SEPARATOR "; ") AS parentTitles,
					   it.description,
					   it.recurdesc,
					   its.deadline
				FROM ' . \F3::get('db_table_prefix') . 'items it
				JOIN ' . \F3::get('db_table_prefix') . 'itemstatus its
				  ON it.itemId = its.itemId
				LEFT JOIN ' . \F3::get('db_table_prefix') . 'lookup lo
				  ON its.itemId = lo.itemId
				JOIN ' . \F3::get('db_table_prefix') . 'items it2
				  ON it2.itemId = lo.parentId
				JOIN ' . \F3::get('db_table_prefix') . 'itemstatus its2
				  ON its2.itemId = it2.itemId
				LEFT JOIN ' . \F3::get('db_table_prefix') . 'context co
				  ON its.contextId = co.contextId
				LEFT JOIN ' . \F3::get('db_table_prefix') . 'timeitems ti
				  ON its.timeframeId = ti.timeframeId
				WHERE its.type = "a"
				  AND its.isSomeday = "n"
				  AND its.dateCompleted IS NULL
				  AND its.nextAction = "y"
				  AND (its.tickleDate <= now()
				  	OR its.tickledate IS NULL)
				  AND (its2.isSomeday IS NULL
					OR its2.isSomeday = "n")
				  AND (its2.tickledate IS NULL
					OR its2.tickledate <= now())
				  AND its2.dateCompleted IS NULL
				GROUP BY it.itemId, co.name, it.title, its.deadline, it.description, it.recurdesc, ti.timeframe
				ORDER BY co.name, it.title';
		
		$rs = \F3::get('db')->exec($sql);

		$actions = array();
        
        foreach($rs as $row) {
            if (empty($row['contextName']))
                $row['contextName'] = "No Context";
            
            $actions[$row['contextName']][] = (object)[
            		'actionId' 		=> $row['actionId'],
            		'title'			=> $row['title'],
            		'parentTitles'	=> $row['parentTitles'],
            		'description' 	=> $row['description'],
            		'timeContext'	=> $row['timeframe'],
            		'deadline'		=> $row['deadline'],
            		'recurdesc'		=> $row['recurdesc']
				];
        }
        
        return $actions;
	}

	public function getNextWaitingFors() {
		$sql = 'SELECT it.itemId AS actionId,
		               it.title,
                       it.description,
                       it.recurdesc,
                       its.deadline,
		               GROUP_CONCAT(DISTINCT it2.title ORDER BY it2.title SEPARATOR "; ") AS parentTitles
				FROM ' . \F3::get('db_table_prefix') . 'items it
				JOIN ' . \F3::get('db_table_prefix') . 'itemstatus its
				  ON it.itemId = its.itemId
				 AND its.type = "w"
				 AND its.isSomeday = "n"
				 AND its.dateCompleted IS NULL
				 AND its.nextAction = "y"
				 AND (its.tickleDate <= now()
				       OR its.tickledate IS NULL)
				LEFT JOIN ' . \F3::get('db_table_prefix') . 'lookup lo
				 ON it.itemId = lo.itemId
				LEFT JOIN ' . \F3::get('db_table_prefix') . 'items it2
				 ON it2.itemId = lo.parentId
				WHERE lo.parentId IS NULL
				   OR lo.parentId IN (
						SELECT its2.itemId
						FROM ' . \F3::get('db_table_prefix') . 'itemstatus its2
						WHERE its2.isSomeday = "n"
						  AND (its2.tickledate IS NULL
						       OR its2.tickledate <= now())
						  AND its2.dateCompleted IS NULL
						  AND its2.type IN ("m", "v", "o", "g", "p")
				   )
				GROUP BY it.itemId, it.title, it.description, it.recurdesc, its.deadline
				ORDER BY it.title';
		$rs = \F3::get('db')->exec($sql);

        $waitingFors = array();
        foreach($rs as $row)
            $waitingFors[] = (object)array(
					'actionId'		=> $row['actionId'],
					'title'			=> $row['title'],
					'parentTitles'	=> $row['parentTitles'],
					'description'	=> $row['description'],
					'deadline'		=> $row['deadline'],
            		'recurdesc'		=> $row['recurdesc']
				);
        
        return $waitingFors;
	}

	public function getInboxItemCount() {
	    $sql = 'SELECT COUNT(its.itemId) AS count
	              FROM ' . \F3::get('db_table_prefix') . 'itemstatus its
	              WHERE its.type = "i"
	                AND its.dateCompleted IS NULL';
		return \F3::get('db')->exec($sql)[0]['count'];
	}

	public function getNextInboxItem() {
	    $query = 'SELECT it.itemId, it.title, it.description
	              FROM ' . \F3::get('db_table_prefix') . 'items it,
	                   ' . \F3::get('db_table_prefix') . 'itemstatus its
	              WHERE it.itemId = its.itemId
	                AND its.type = "i"
	                AND its.dateCompleted IS NULL
	              ORDER BY its.dateCreated
	              LIMIT 1';
	    $rows = \F3::get('db')->exec($query);
        
        return $rows
        	? (object)array(
        			'itemId' 		=> $rows[0]['itemId'],
        			'title'			=> $rows[0]['title'],
        			'description'	=> $rows[0]['description']
        		)
        	: null;
	}

	public function getInboxItem($itemId) {
	    $query = 'SELECT it.itemId, it.title, it.description
                  FROM ' . \F3::get('db_table_prefix') . 'items it
                  WHERE it.itemId = :itemId';
        $rows = \F3::get('db')->exec($query, array(
    			'itemId' => $itemId
    		));
	    
        return $rows
        	? (object)array(
        			'itemId' 		=> $rows[0]['itemId'],
        			'title'			=> $rows[0]['title'],
        			'description'	=> $rows[0]['description']
        		)
        	: null;
	}

	/**
	 * Creates a new Inbox item in the system.
	 * 
	 * @param $title Title of the new item.
	 * @param $description Description of the new item.
	 */
	public function createInboxItem($title, $description) {
		\F3::get('db')->begin();

	    $query = 'INSERT INTO ' . \F3::get('db_table_prefix') . 'items
	              (title, description)
	              VALUES (:title, :description)';
	    \F3::get('db')->exec($query, array('title' => $title, 'description' => $description));
	    
	    $newItemId = \F3::get('db')->lastInsertID();

	    $query = 'INSERT INTO ' . \F3::get('db_table_prefix') . 'itemstatus
	              (itemId, dateCreated, lastModified, type)
	              VALUES (:newItemId, NOW(), NOW(), "i")';
	    \F3::get('db')->exec($query, array('newItemId' => $newItemId));
	    
	    \F3::get('db')->commit();

	    return $newItemId;
	}

	/**
	 * Mark an item as completed. Handle recurrence, if needed, by creating a
	 * new item and setting up its tickler/deadline dates.
	 * 
	 * @param $itemId ID of the item to be marked as completed.
	 */
	public function markItemCompleted($itemId) {
	    $completionDate = date('Y-m-d');
	    
	    /* Select the completed item to check for recurrence */
	    $query = 'SELECT it.recur, its.deadline, its.tickledate
	              FROM ' . \F3::get('db_table_prefix') . 'items it,
	                   ' . \F3::get('db_table_prefix') . 'itemstatus its
	              WHERE it.itemId = :itemId
	                AND it.itemId = its.itemId';
        $row = \F3::get('db')->exec($query, array('itemId' => $itemId))[0];
	    
	    if (!empty($row['recur'])) {
	        /* It's a recurring item, need to handle this */
	        /* First, copy the item */
	        $newItemId = $this->copyItem($itemId);
	        
	        /* Second, check which date to recur from */
	        if (preg_match("/^FREQ=(YEARLY|MONTHLY|WEEKLY|DAILY);INTERVAL=[0-9]+$/", $row['recur'])) {
                /* very simple recurrence, so recur from dateCompleted */
                $startdate = $completionDate;
            } else if (empty($row['deadline']) || $row['deadline'] === 'NULL') {
                /* no deadline, so recur from tickler if available, and fall
                 * back to date completed */
                $startdate = (empty($row['tickledate']))
                    ? $completionDate
                    : $row['tickledate'];
            } else {
                /* recur from deadline */
                $startdate = $row['deadline'];
            }
            if (empty($startdate) || $startdate === 'NULL') {
                /* if we still haven't got a start date, use today */
                $startdate = $completionDate;
            }
	        
            /* Fetch the next date */
	        $newDate = \helpers\RecurrenceTool::getNextRecurrence(
                    $row['recur'], $startdate);
            
            if (!empty($newDate)) {
                /* Check which date we'll be updating - tickler or deadline */
                if (empty($row['deadline']) || $row['deadline'] === 'NULL') {
                    /* No deadline, so the new date becomes the tickle date */
                    $tickleDate = $newDate;
                    $deadline = null;
                } else {
                    /* New date becomes the deadline */
                    if ($row['tickledate'] !== 'NULL') {
                        /* Tickle date was present, so set it to the same amount
                         * of time before the deadline as the original had */
                        $tickleDate = date("Y-m-d" , strtotime($row['tickledate'])
                                + (strtotime($newDate) - strtotime($row['deadline']))
                                );
                    }
                    $deadline = $newDate;
                }
                
                /* Update the dates in the table */
                $query = 'UPDATE ' . \F3::get('db_table_prefix') . 'itemstatus
                          SET lastModified = NOW(),
                              tickledate = :tickleDate,
                              deadline = :deadline
                          WHERE itemId = :newItemId';
        		\F3::get('db')->exec($query, array(
	        			'newItemId' => $newItemId,
	        			'tickleDate' => $tickleDate,
	        			'deadline' => $deadline
	        		));
            }
	    }
	    
	    /* Set the selected item to completed */
		$query = 'UPDATE ' . \F3::get('db_table_prefix') . 'itemstatus
		          SET lastModified = NOW(),
		              dateCompleted = :completionDate
		          WHERE itemId = :itemId';
		\F3::get('db')->exec($query, array(
    			'itemId' => $itemId,
    			'completionDate' => $completionDate
    		));
	}

	/**
	 * Creates an exact copy of a given item, with only the created/updated
	 * dates changed.
	 * 
	 * @param $itemId ID of the item to be copied.
	 * @return Returns the new item's ID.
	 */
	public function copyItem($itemId) {
		
		\F3::get('db')->begin();

	    /* Copy the item */
	    $query = 'INSERT INTO ' . \F3::get('db_table_prefix') . 'items
	              (title, description, desiredOutcome, recurdesc, recur)
	              SELECT title, description, desiredOutcome, recurdesc, recur
	              FROM ' . \F3::get('db_table_prefix') . 'items it
	              WHERE it.itemId = :itemId';
		\F3::get('db')->exec($query, array(
    			'itemId' => $itemId
    		));
        
        $newItemId = \F3::get('db')->lastInsertId();
        
        /* Copy the item's properties, update created and modified dates to current */
        $query = 'INSERT INTO ' . \F3::get('db_table_prefix') . 'itemstatus
                  (itemId, dateCreated, lastModified, dateCompleted, type, categoryId,
                   isSomeday, contextId, timeFrameId, deadline, tickledate,
                   nextaction)
                  SELECT :newItemId, NOW(), NOW(), dateCompleted, type, categoryId,
                   isSomeday, contextId, timeFrameId, deadline, tickledate,
                   nextaction
                  FROM ' . \F3::get('db_table_prefix') . 'itemstatus its
                  WHERE its.itemId = :itemId';
		\F3::get('db')->exec($query, array(
    			'itemId' => $itemId,
    			'newItemId' => $newItemId
    		));

        /* Copy the item's relationships */
        $query = 'INSERT INTO ' . \F3::get('db_table_prefix') . 'lookup
                  (parentId, itemId)
                  SELECT parentId, :newItemId
                  FROM ' . \F3::get('db_table_prefix') . 'lookup lo
                  WHERE lo.itemId = :itemId';
		\F3::get('db')->exec($query, array(
    			'itemId' => $itemId,
    			'newItemId' => $newItemId
    		));
        
		\F3::get('db')->commit();

        return $newItemId;
	}

	/**
     * Deletes a given item from the database.
     * 
     * @param $itemId ID of the item to be deleted.
     */
	public function deleteItem($itemId) {
		\F3::get('db')->begin();

	    /* Remove referenes in lookup table */
	    $query = 'DELETE FROM ' . \F3::get('db_table_prefix') . 'lookup
	              WHERE itemId = :itemId
	                 OR parentId = :itemId';
		\F3::get('db')->exec($query, array(
    			'itemId' => $itemId
    		));
	    
        /* Remove the status row */
        $query = 'DELETE FROM ' . \F3::get('db_table_prefix') . 'itemstatus
                  WHERE itemId = :itemId';
		\F3::get('db')->exec($query, array(
    			'itemId' => $itemId
    		));
        
        /* Remove the item row */
        $query = 'DELETE FROM ' . \F3::get('db_table_prefix') . 'items
                  WHERE itemId = :itemId';
		\F3::get('db')->exec($query, array(
    			'itemId' => $itemId
    		));

        \F3::get('db')->commit();
	}

	public function getCategories() {
        $query = 'SELECT ca.categoryId, ca.category
                  FROM ' . \F3::get('db_table_prefix') . 'categories ca
                  ORDER BY ca.category';
        $rows = \F3::get('db')->exec($query);

        $contexts = array();
        foreach($rows as $row)
            $contexts[] = (object)array(
            	'categoryId' => $row['categoryId'],
            	'name' => $row['category']
        	);
        
        return $contexts;
    }

    public function getSpaceContexts() {
        $query = 'SELECT co.contextId, co.name
                  FROM ' . \F3::get('db_table_prefix') . 'context co
                  ORDER BY co.name';
        $rows = \F3::get('db')->exec($query);
        
        $contexts = array();
        foreach($rows as $row)
            $contexts[] = (object)array(
            	'contextId' => $row['contextId'],
            	'name' => $row['name']
            );
        
        return $contexts;
    }
    
    public function getTimeContexts() {
        $query = 'SELECT ti.timeframeId, ti.timeframe
                  FROM ' . \F3::get('db_table_prefix'). 'timeitems ti
                  ORDER BY ti.timeframe';
        $rows = \F3::get('db')->exec($query);
        
        $contexts = array();
        foreach($rows as $row)
            $contexts[] = (object)array(
            	'contextId' => $row['timeframeId'],
            	'name' => $row['timeframe']
            );
        
        return $contexts;
    }

    public function findParents($parentNamePart, array $parentTypes = array('m', 'v', 'o', 'g', 'p')) {
	    $query = 'SELECT it.itemId,
	                     its.categoryId,
	                     it.title,
	                     its.deadline,
	                     its.type
	              FROM ' . \F3::get('db_table_prefix') . 'items it,
                       ' . \F3::get('db_table_prefix') . 'itemstatus its
                  WHERE it.itemId = its.itemId
                    AND its.type IN (' . $this->implodeForSql($parentTypes) . ')
                    AND it.title LIKE :parentNamePart
                    AND its.dateCompleted IS NULL
                  ORDER BY it.title
                  LIMIT 10';
		$rows = \F3::get('db')->exec($query, array(
    			'parentNamePart' => '%' . $parentNamePart . '%'
    		));

	    $parents = array();
	    foreach($rows as $row) {
	    	$parents[] = (object)array(
	    			'id' => $row['itemId'],
                    'categoryId' => $row['categoryId'],
                    'title' => $row['title'],
                    'deadline' => $row['deadline'],
                    'type' => \F3::get('lang.ItemType_' . $row['type'])
	    		);
        }
        return $parents;
	}

	private function implodeForSql(array $source) {
		foreach ($source as $element)
			$out[] = \F3::get('db')->quote($element);
		return implode(',', $out);
	}

	public function getItem($itemId) {
		$query = 'SELECT it.itemId,
						 it.title,
						 it.desiredOutcome,
						 it.recur,
						 it.recurdesc,
						 its.categoryId,
						 its.contextId,
						 its.timeframeId,
						 its.deadline
                  FROM ' . \F3::get('db_table_prefix') . 'items it
                  JOIN ' . \F3::get('db_table_prefix') . 'itemstatus its
                    ON it.itemId = its.itemId
                  WHERE it.itemId = :itemId';
		$rows = \F3::get('db')->exec($query, array(
    			'itemId' => $itemId
    		));
	    
        return $rows
        	? (object)array(
        			'itemId' 			=> $rows[0]['itemId'],
        			'title'				=> $rows[0]['title'],
        			'desiredOutcome'	=> $rows[0]['desiredOutcome'],
        			'recur'				=> $rows[0]['recur'],
					'recurdesc'			=> $rows[0]['recurdesc'],
					'categoryId'		=> $rows[0]['categoryId'],
					'contextId'			=> $rows[0]['contextId'],
					'timeframeId'		=> $rows[0]['timeframeId'],
					'deadline'			=> $rows[0]['deadline']
        		)
        	: null;
	}

	public function createItem(array $props) {
		\F3::get('db')->begin();

        $query = 'INSERT INTO ' . \F3::get('db_table_prefix') . 'items
                         (title,
                          description,
                          desiredOutcome,
                          recur,
                          recurdesc)
                  VALUES (:title,
                          :description,
                          :outcome,
                          :recur,
                          :recurDesc)';
       	\F3::get('db')->exec($query, array(
       			'title' 		=> $props['title'],
       			'description' 	=> empty($props['description']) ? '' : $props['description'],
       			'outcome' 		=> empty($props['outcome']) ? null : $props['outcome'],
       			'recur' 		=> empty($props['recur']) ? null : $props['recur'],
       			'recurDesc' 	=> empty($props['recurDesc']) ? null : $props['recurDesc']
       		));
        
        $newItemId = \F3::get('db')->lastInsertID();
        
        $query = 'INSERT INTO ' . \F3::get('db_table_prefix') . 'itemstatus
                         (itemId,
                          dateCreated,
                          lastModified,
                          type,
                          categoryId,
                          isSomeday,
                          contextId,
                          timeframeId,
                          deadline,
                          tickledate,
                          nextaction)
                  VALUES (:itemId,
                          NOW(),
                          NOW(),
                          :type,
                          :categoryId,
                          :isSomeday,
                          :spaceContextId,
                          :timeContextId,
                          :deadline,
                          :tickleDate,
                          :isNext)';
       	\F3::get('db')->exec($query, array(
       			'type' 				=> $props['type'],
       			'categoryId' 		=> empty($props['categoryId']) ? 0 : $props['categoryId'],
       			'isSomeday' 		=> !empty($props['isSomeday']) && 'on' == $props['isSomeday'] ? 'y' : 'n',
       			'spaceContextId'	=> empty($props['spaceContextId']) ? 0 : $props['spaceContextId'],
       			'timeContextId' 	=> empty($props['timeContextId']) ? 0 : $props['timeContextId'],
       			'deadline'			=> empty($props['deadline']) ? null : $props['deadline'],
       			'tickleDate'		=> empty($props['tickleDate']) ? null : $props['tickleDate'],
       			'isNext'			=> !empty($props['isNext']) && 'on' == $props['isNext'] ? 'y' : 'n',
       			'itemId' 			=> $newItemId
       		));

        /* Add parents */
        $query = 'INSERT INTO ' . \F3::get('db_table_prefix') . 'lookup (itemId, parentId)
                  VALUES (:itemId, :parentId)';
        $st = \F3::get('db')->prepare($query);
        
        foreach ($props['parentIds'] as $parentId) {
        	$st->execute(array(
       			'itemId' 	=> $newItemId,
       			'parentId' => $parentId
       		));
        }

        \F3::get('db')->commit();

		return $newItemId;
    }

	public function updateItem($itemId, array $props) {
    	\F3::get('db')->begin();

        $query = 'UPDATE ' . \F3::get('db_table_prefix') . 'items
                  SET title = :title,
                      description = :description,
                      desiredOutcome = :outcome,
                      recur = :recur,
                      recurdesc = :recurDesc
                  WHERE itemId = :itemId';
       	\F3::get('db')->exec($query, array(
       			'title' 		=> $props['title'],
       			'description' 	=> empty($props['description']) ? null : $props['description'],
       			'outcome' 		=> empty($props['outcome']) ? null : $props['outcome'],
       			'recur' 		=> empty($props['recur']) ? null : $props['recur'],
       			'recurDesc' 	=> empty($props['recurDesc']) ? null : $props['recurDesc'],
       			'itemId' 		=> $itemId
       		));

       	$query = 'UPDATE ' . \F3::get('db_table_prefix') . 'itemstatus
                  SET lastModified = NOW(),
                      type = :type,
                      categoryId = :categoryId,
                      isSomeday = :isSomeday,
                      contextId = :spaceContextId,
                      timeframeId = :timeContextId,
                      deadline = :deadline,
                      tickledate = :tickleDate,
                      nextaction = :isNext
                  WHERE itemId = :itemId';
       	\F3::get('db')->exec($query, array(
       			'type' 				=> $props['type'],
       			'categoryId' 		=> empty($props['categoryId']) ? 0 : $props['categoryId'],
       			'isSomeday' 		=> !empty($props['isSomeday']) && 'on' == $props['isSomeday'] ? 'y' : 'n',
       			'spaceContextId'	=> empty($props['spaceContextId']) ? 0 : $props['spaceContextId'],
       			'timeContextId' 	=> empty($props['timeContextId']) ? 0 : $props['timeContextId'],
       			'deadline'			=> empty($props['deadline']) ? null : $props['deadline'],
       			'tickleDate'			=> empty($props['tickleDate']) ? null : $props['tickleDate'],
       			'isNext'			=> !empty($props['isNext']) && 'on' == $props['isNext'] ? 'y' : 'n',
       			'itemId' 			=> $itemId
       		));

        /* Delete those that are not on the list */
        $query = 'DELETE FROM ' . \F3::get('db_table_prefix') . 'lookup
                  WHERE itemId = :itemId
                    AND parentId NOT IN (:parentIds)';
       	\F3::get('db')->exec($query, array(
       			'itemId' 	=> $itemId,
       			'parentIds' => implode(',', $props['parentIds'])
       		));
        
        /* Fetch the remaining parent id's stored in database */
        $query = 'SELECT parentId
                  FROM ' . \F3::get('db_table_prefix') . 'lookup lo
                  WHERE itemId = :itemId';
       	$rows = \F3::get('db')->exec($query, array(
       			'itemId' 	=> $itemId,
       		));

        $existingParentIds = array();
        foreach ($rows as $row)
            $existingParentIds[] = $row['parentId'];
        
        /* Add missing ones from the list */
        $query = 'INSERT INTO ' . \F3::get('db_table_prefix') . 'lookup (itemId, parentId)
                  VALUES (:itemId, :parentId)';
        $st = \F3::get('db')->prepare($query);
        
        foreach ($props['parentIds'] as $parentId) {
            /* Omit existing parent id's */
            if (false == array_search($parentId, $existingParentIds)) {
            	$st->execute(array(
	       			'itemId' 	=> $itemId,
	       			'parentId' => $parentId
	       		));
            }
        }

        \F3::get('db')->commit();
    }

    public function getItemsPastDue($itemType) {
		$sql = 'SELECT it.itemId,
					   it.title,
					   its.deadline
				FROM ' . \F3::get('db_table_prefix') . 'items it
				JOIN ' . \F3::get('db_table_prefix') . 'itemstatus its
				  ON it.itemId = its.itemId
				 AND its.type = ' . \F3::get('db')->quote($itemType) . '
				 AND its.deadline < CURDATE()
				 AND its.dateCompleted IS NULL
				 AND its.isSomeday = "n"
				 AND its.nextAction = "y"
				 AND (its.tickleDate IS NULL OR its.tickleDate <= CURDATE())
				LEFT JOIN ' . \F3::get('db_table_prefix') . 'lookup lo
					   ON its.itemId = lo.itemId
				JOIN ' . \F3::get('db_table_prefix') . 'itemstatus its2
				  ON its2.itemId = lo.parentId
				 AND its2.dateCompleted IS NULL
				 AND its2.isSomeday = "n"
				 AND (its2.tickleDate IS NULL OR its2.tickleDate <= CURDATE())
				GROUP BY it.itemId
				ORDER BY its.deadline
				';
		$rows = \F3::get('db')->exec($sql);

		$items = [];
		foreach ($rows as $row) {
			$items[] = (object)[
				'id' => $row['itemId'],
				'title' => $row['title'],
				'deadline' => $row['deadline']
			];
		}

		return $items;
    }

    public function getProjectsWithoutOutcomes() {
    	$sql = 'SELECT it.itemId,
    				   it.title
				FROM ' . \F3::get('db_table_prefix') . 'items it
				JOIN ' . \F3::get('db_table_prefix') . 'itemstatus its
				  ON it.itemId = its.itemId
				WHERE its.type = "p"
				  AND (it.desiredOutcome = "" OR it.desiredOutcome IS NULL)
				  AND its.isSomeday = "n"
				  AND its.dateCompleted IS NULL
				  AND (its.tickleDate IS NULL OR its.tickleDate <= CURDATE())
				ORDER BY it.title
				';
		$rows = \F3::get('db')->exec($sql);

		$projects = [];
		foreach ($rows as $row) {
			$projects[] = (object)[
				'id' => $row['itemId'],
				'title' => $row['title']
			];
		}

		return $projects;
    }

    public function getProjectsMissingNextActions() {
    	$sql = 'SELECT it.itemId,
    				   it.title
				FROM ' . \F3::get('db_table_prefix') . 'items it
				JOIN ' . \F3::get('db_table_prefix') . 'itemstatus its
				  ON it.itemId = its.itemId
				 AND its.`type` = "p"
				 AND its.isSomeday = "n"
				 AND its.dateCompleted IS NULL
				 AND (its.tickleDate IS NULL OR its.tickleDate <= CURDATE())
				WHERE it.itemId NOT IN (
					SELECT DISTINCT lo.parentId
					FROM ' . \F3::get('db_table_prefix') . 'lookup lo
					JOIN ' . \F3::get('db_table_prefix') . 'itemstatus its
				  	  ON lo.parentId = its.itemId
					 AND its.`type` = "p"
					 AND its.isSomeday = "n"
					 AND its.dateCompleted IS NULL
				 	 AND (its.tickleDate IS NULL OR its.tickleDate <= CURDATE())
					JOIN ' . \F3::get('db_table_prefix') . 'itemstatus its2
					  ON lo.itemId = its2.itemId
					 AND its2.`type` in ("a", "w")
					 AND its2.dateCompleted IS NULL
					 AND its2.nextaction = "y"
					)
				ORDER BY it.title
				';
    	$rows = \F3::get('db')->exec($sql);

		$projects = [];
		foreach ($rows as $row) {
			$projects[] = (object)[
				'id' => $row['itemId'],
				'title' => $row['title']
			];
		}

		return $projects;
    }

}
