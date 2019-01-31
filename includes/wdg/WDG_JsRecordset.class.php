<?php
/*
 * ADOBE SYSTEMS INCORPORATED
 * Copyright 2007 Adobe Systems Incorporated
 * All Rights Reserved
 * 
 * NOTICE:  Adobe permits you to use, modify, and distribute this file in accordance with the 
 * terms of the Adobe license agreement accompanying it. If you have received this file from a 
 * source other than Adobe, then your use, modification, or distribution of it requires the prior 
 * written permission of Adobe.
 */

/*
	Copyright (c) InterAKT Online 2000-2006. All rights reserved.
*/

class WDG_JsRecordset {
	var $outputString = '';
	
	function WDG_JsRecordset($RecordsetName) {
		$rs = $GLOBALS[$RecordsetName];
		if (is_resource($rs)) {
			$recordset = new KT_Recordset($rs); 
		} else {
			$recordset = &$rs;
		}
		$nl = "\r\n";
		$this->outputString .= <<<EOD
<script>
top.jsRawData_{$RecordsetName} = [

EOD;
		$fieldCount = $recordset->FieldCount();
		$fieldNames = array();
		for ($i=0; $i < $fieldCount; $i++) {
			$meta = $recordset->FetchField($i);
			if ($meta) {
				$fieldNames[] = $meta->name;
				$this->outputString .= ($i==0 ? '[' : ', '). '"' . $meta->name . '"';
			}
		}
		$this->outputString .= <<<EOD
],
//data

EOD;
		while (!$recordset->EOF) {
			$arr = array();
			foreach ($fieldNames as $field) {
				$arr[] = $recordset->Fields($field);
			}
			$this->outputString .= WDG_phparray2jsarray($arr) . ', ';
			$recordset->MoveNext();
		}
		
		$this->outputString .= <<<EOD
[]
];
top.{$RecordsetName} = new JSRecordset('{$RecordsetName}');
</script>
EOD;

		//restore old rs position
		$recordset->MoveFirst();
	}
	function getOutput() {
		return $this->outputString;
	}
}
?>