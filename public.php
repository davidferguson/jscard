<html>
	<head>
		<link rel="shortcut icon" href="logo.ico" />
		<title>Public Stacks :: jsCard</title>
		<style type="text/css">
			body
			{
			background-color: #607AC5;
			background-repeat: repeat-y;
			background-image: url( 'images/bkgnd.jpg' );
			background-position: center center;
			}

			#stage
			{
			position: absolute;
			top: 0px;
			width: 705px;
			height: 100%;
			left: 50%;
			margin-left: -352px;
			}

			#content
			{
			position: absolute;
			top: 0px;
			left: 50%;
			margin-left: -352px;
			width: 701px;
			}

			#maincolumn
			{
			position: absolute;
			top: 242px;
			left: 30px;
			width: 600px;
			font-family: "Trebuchet MS", Verdana, Arial, sans-serif;
			font-size: 12px;
			line-height: 22px;
			text-align: justify;
			}
			#maincolumn h1
			{
				font-size: 18px;
			}

			.errnote
			{
				font-size: 18px;
				padding: 10px;
				color: #B34F4F;
				background-color: #F4E1E1;
			}

			.tipnote
			{
				font-size: 18px;
				padding: 10px;
				color: #4E7437;
				background-color: #ECFFE0;
			}
		</style>
		<script type="text/javascript">
			/* Client-side access to querystring name=value pairs
				Version 1.2.3
				22 Jun 2005
				Adam Vandenberg
			*/
			function Querystring(qs)
			{
				this.params = new Object()
				this.get=Querystring_get
				if (qs == null)
					qs=location.search.substring(1,location.search.length)
				if (qs.length == 0) return
			// Turn <plus> back to <space>
			// See: http://www.w3.org/TR/REC-html40/interact/forms.html#h-17.13.4.1
				qs = qs.replace(/\+/g, ' ')
				var args = qs.split('&') // parse out name/value pairs separated via &
			// split out each name=value pair
				for (var i=0;i<args.length;i++) {
					var value;
					var pair = args[i].split('=')
					var name = unescape(pair[0])
					if (pair.length == 2)
						value = unescape(pair[1])
					else
						value = name
					this.params[name] = value
				}
			}
			
			function Querystring_get(key, default_)
			{
				// This silly looking line changes UNDEFINED to NULL
				if (default_ == null) default_ = null;
				var value=this.params[key]
				if (value==null) value=default_;
				return value
			}
		</script>
	</head>
	<body>
		<div id="stage"><br/></div>
		<div id="content">
			<img src="images/header.jpg" border="0" />
			<div id="maincolumn">
				<script type="text/javascript">
				var qs = new Querystring();
				</script>			
				<h1>Welcome to the Public stacks.</h1>
				<p>These are stacks that someone has made available to the entire world to see. Why don't you create a stack and publish yours?</p>
				<p>
					To publish a stack, first log in, open the stack you want to make public, and then click on the <i>Publish</i> icon in the options menu. Your
					stack will then be available here for anyone to see.
				</p>
				<div align=center>
					<table>
						<?php
							// Connect to server and select database.
							include('mysql_connect.php');
							$sql="SELECT * FROM stacks WHERE public = 1";
							$result=mysql_query($sql);
							while($rows=mysql_fetch_array($result))
							{
								$stack_usersid = $rows['users_id'];
								$stackid = $rows['id'];
								$url = "stack.php?stack=" . $stackid;
								$query = mysql_query("SELECT * FROM users WHERE id=$stack_usersid");
								$numrows = mysql_num_rows($query);
								$row = mysql_fetch_assoc($query);
								?>
								<tr>
									<td width="600"><b><a href='<?php echo $url; ?>'><? echo $rows['name']; ?></b></a></td>
									<td width="114">by <? echo $row['username']; ?></td>
								</tr>
								<tr>
									<td><font size="2"><? echo $rows['public_description']; ?></font></td>
								</tr>
								</br>
								<?php
							}
							mysql_close(); //close database
						?>
					</table>
				</div>
			</div>
		</div>
	</body>
</html>