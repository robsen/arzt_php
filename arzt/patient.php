<?php
	require_once 'includes/session.inc.php';
	require_once 'includes/db.inc.php';
	
	if (isset($_POST['patient-search']))
	{
		DB::Call('search_patient', []);
	}
	
	if (isset($_GET['create'])
		&& isset($_POST['patient'])
		&& isset($_POST['firstname'])
		&& isset($_POST['lastname'])
		&& isset($_POST['svnr']))
	{
		DB::Call(
			'create_patient',
			[
				$_POST['firstname'],
				$_POST['lastname'],
				$_POST['svnr'],
				$_SESSION['userId']
			]
		);
	}
	else if (isset($_GET['show']))
	{
		$patients = DB::Call('get_all_patients');
	}
	else if (isset($_GET['modify']))
	{
		if (!empty($_GET['modify'])
			&& is_numeric($_GET['modify'])
			&& $_GET['modify'] > 0)
		{
			$patient = DB::Call('get_patient_by_id', [$_GET['modify']], false);
		}
		else
		{
			$_GET['message'] = 'Dieser Patient existiert nicht!';
		}
	}
?>
<!DOCTYPE html>
<html lang=de>
	<head>
		<meta charset=utf-8>
		<title>Arzt - Dashboard</title>
		
		<link rel=stylesheet href=/css/main.css>
		<link rel=stylesheet href=/css/patient.css>
	</head>
	<body>
		<?php require_once 'includes/menu.inc.htm' ?>
		
		<?php if (isset($_GET['create']) || isset($patient)): ?>
		<h1>Patienten <?= isset($patient) ? 'bearbeiten' : 'anlegen' ?></h1>
		<form action=/patient.php?create method=post>
			<label for=firstname>Vorname</label>
			<input type=text id=firstname name=firstname value="<?= isset($patient['firstname']) ? $patient['firstname'] : null ?>" autofocus required>
			
			<label for=lastname>Nachname</label>
			<input type=text id=lastname name=lastname value="<?= isset($patient['lastname']) ? $patient['lastname'] : null ?>" required>
			
			<label for=svnr>Sozialversicherungs-Nr.</label>
			<input type=text id=svnr name=svnr value="<?= isset($patient['svnr']) ? $patient['svnr']: null ?>" required>
			
			<input type=submit name=patient value="<?= isset($patient) ? 'Änderungen Speichern' : 'Neuen Patienten anlegen' ?>" id=submit-patient>
		</form>
		<?php endif ?>
		
		<?php if (isset($_GET['show'])): ?>
		<h1>Patienten Übersicht</h1>
		<form action=/patient.php method=post id=patient-search-form>
			<table>
				<thead>
					<tr>
						<th>Nachname</th>
						<th>Vorname</th>
						<th title=Sozialversicherungs-Nummer>SV-Nr</th>
						<th>Interaktion</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($patients as $patient): ?>
					<tr>
						<td><?= $patient['lastname'] ?></td>
						<td><?= $patient['firstname'] ?></td>
						<td><?= $patient['svnr'] ?></td>
						<td>
							<a href="/patient.php?modify=<?= $patient['id'] ?>" title=Bearbeiten>#</a>
							<a href="/patient.php?delete=<?= $patient['id'] ?>" title=Löschen>X</a>
						</td>
					</tr>
					<?php endforeach ?>
					<tr>
						<td><input type=text name=lastname placeholder=Nachname></td>
						<td><input type=text name=firstname placeholder=Vorname></td>
						<td><input type=text name=svnr placeholder=SV-Nr.></td>
						<td><input type=submit name=patient-search value=Suchen></td>
					</tr>
				</tbody>
			</table>
		</form>
		<?php endif ?>
		
		<?php if (isset($_GET['message'])): ?>
		<p id=message><?= htmlspecialchars($_GET['message']) ?></p>
		<?php endif ?>
	</body>
</html>
<?php