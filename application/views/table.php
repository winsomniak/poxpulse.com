<table class="dataTable">
	<thead>
		<tr>
			<?php foreach($headings as $key => $value)
			{
				echo '<th>' . $value . '</th>' . "\r\n";
			}
			?>
		</tr>
	</thead>
	<tbody>	
			<?php foreach($items as $key => $value)
			{
				echo '<tr>' . "\r\n";
				if(count($value) > 1)
				{
					foreach($value as $key => $value)
					{
						echo '<td>' . $value . '</td>' . "\r\n";
					}
				}
				else
				{
					echo '<td>' . $value . '</td>' . "\r\n";
				}
				echo '</tr>';
			}
			?>
	</tbody>
</table>
	