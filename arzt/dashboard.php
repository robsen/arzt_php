<?php
	require_once 'includes/session.inc.php';
	
	require_once 'includes/db.inc.php';
	$patients = DB::Call('get_patients_in_treatment');
?>
<!DOCTYPE html>
<html lang=de>
	<head>
		<meta charset=utf-8>
		<title>Arzt - Dashboard</title>
		
		<link rel=stylesheet href=/css/main.css>
		<link rel=stylesheet href=/css/dashboard.css>
	</head>
	<body>
		<?php require_once 'includes/menu.inc.htm' ?>
		
		<section id=main-section>
			<header>
				<h1>Derzeitige Patientenabfertigung</h1>
			</header>
			<form action=/dashboard.php?create method=post>
				<?php if (isset($patients)): ?>
				<table>
					<thead>
						<tr>
							<th>Nachname</th>
							<th>Vorname</th>
							<th>Behandlungsbegin</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($patients as $patient): ?>
						<tr>
							<td><?= $patient['lastname'] ?></td>
							<td><?= $patient['firstname'] ?></td>
							<td><?= $patient['begin'] ?></td>
						</tr>
						<?php endforeach ?>
					</tbody>
				</table>
				<?php else: ?>
				<p>Zur Zeit werden keine Patienten betreut.</p>
				<?php endif ?>
				
				<!-- <input type=submit name=new_treatment id=add-treatment-btn value="Patienten hinzufügen"> -->
			</form>
			
			<a class=button id=add-treatment href=/patient.php?show=0>Patienten hinzufügen</a>
		</section>
		
		<?php if (isset($_GET['message'])): ?>
		<p id=message><?= htmlspecialchars($_GET['message']) ?></p>
		<?php endif ?>
	</body>
</html>
<?php