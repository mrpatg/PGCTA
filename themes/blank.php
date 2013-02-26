<?php
		/**
		// PG CTA Theme PHP Structure
		// 
		// Options Definitions
		// %PGCTAURL% - URL value input from the PGCTA settings screen.
		// %PGCTAIMG% - URL value of the uploaded/associated image.
		// %PGCTATOP% - Contents of the default message box. (text/html, overridden by post if meta data is optioned)
		// %PGCTABOTTOM% - Contents of the default message box. (text/html, overridden by post if meta data is optioned)
		// 
		*/

echo '<a href="%PGCTAURL%" class="PGCTA-structure-container" >
	<span class="PGCTA-structure-image">
		<img src="%PGCTAIMG%">
	</span>
	<span class="PGCTA-structure-messagebox">
		
		<span class="PGCTA-message-toptext">%PGCTATOP%</span>
		<span class="PGCTA-message-bottomtext">%PGCTABOTTOM%</span>
	</span>
</a>';


?>