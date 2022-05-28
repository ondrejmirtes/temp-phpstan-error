<?php
/**
 * A renderer that will get an iterable object 
 * and make it into an html table <body> piece
 * 
 * @author itaymoav
 *
 */
class ViewSnipets_Data_SimpleTableBody{
	public function render(Iterator $data,callable $process_func=null,$additional_info=null){
		if(!$process_func){
			$process_func = function($row){
				return ['row_level'=> '','data' =>$row];
			};
		}
		
		echo '<body>';
		foreach($data as $row):
			$row = $process_func($row,$additional_info);
			echo "<tr {$row['row_level']}>";
			foreach($row['data'] as $column):
				echo "<td>{$column}</td>";
			endforeach;
			echo '</tr>';
		endforeach;
		echo '</body>';
	}
} 
