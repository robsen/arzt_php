<?php
	require_once 'includes/session.inc.php';
	require_once 'includes/db.inc.php';
	
	if (isset($_POST['patient-search']))
	{
		//DB::Call('search_patient', []);
	}
	
	// create new patient
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
		
		header('location:/patient.php?show');
		exit(0);
	}
	// patient list
	else if (isset($_GET['show']))
	{
		// get specific patient
		if (!empty($_GET['show'])
			&& is_numeric($_GET['show'])
			&& $_GET['show'] > 0)
		{
			$patient = DB::Call('get_patient_by_id', [$_GET['show']], false);
		}
		// get all patients who are not in treatment
		else if ($_GET['show'] === '0')
		{
			$addTreatment = true; // for GUI => enable button
			$patients = DB::Call('get_patients_none_treatment');
		}
		// get all patients
		else
		{
			$patients = DB::Call('get_all_patients');
		}
	}
	// add patient to treatment list
	else if (isset($_GET['cure']))
	{
		if (!empty($_GET['cure'])
			&& is_numeric($_GET['cure'])
			&& $_GET['cure'] > 0)
		{
			$patient = DB::Call('start_treatment_of_patient', [$_GET['cure'], $_SESSION['userId']]);
		}
		
		header('location:/dashboard.php');
		exit(0);
	}
	// show specific patients data in form
	else if (isset($_GET['modify']))
	{
		if (empty($_GET['modify']))
		{
			header('location:/patient.php?show');
			exit(0);
		}
		// do we've a valid patient ID?
		else if (is_numeric($_GET['modify'])
				&& $_GET['modify'] > 0)
		{
			$patient = DB::Call('get_patient_by_id', [$_GET['modify']], false);
			// none existing patient?
			if (empty($patient))
			{
				$_GET['message'] = 'Dieser Patient existiert nicht!';
			}
			// existing patient will be now displayed in the form,
			// since $patient is set with valid data
		}
		else
		{
			$_GET['message'] = 'Dieser Patient existiert nicht!';
		}
	}
	// update specific patients data according to form
	else if (isset($_GET['update']))
	{
		// no patient ID submitted?
		if (empty($_GET['update']))
		{
			header('location:/patient.php?show');
			exit(0);
		}
		// is submitted patient ID valid?
		else if (is_numeric($_GET['update'])
				&& $_GET['update'] > 0)
		{
			// form completed?
			if (isset($_POST['firstname'])
				&& isset($_POST['lastname'])
				&& isset($_POST['svnr']))
			{
				$patient = DB::Call('get_patient_by_id', [$_GET['update']], false);
				// existing patient?
				if (!empty($patient))
				{
					DB::Call(
						'modify_patient_with_id',
						[
							$patient['id'],
							$_POST['firstname'],
							$_POST['lastname'],
							$_POST['svnr']
						]
					);
					$_GET['message'] = 'Daten des Patienten aktualisiert';
					header('location:/patient.php?show');
					exit(0);
				}
				else
				{
					$_GET['message'] = 'Dieser Patient existiert nicht!';
				}
			}
			else
			{
			}
		}
		else
		{
			$_GET['message'] = 'Dieser Patient existiert nicht!';
		}
		
		header('location:/patient.php?show');
		exit(0);
	}
	// delete specific patient
	else if (isset($_GET['delete']))
	{
		if (empty($_GET['delete']))
		{
			header('location:/patient.php?show');
			exit(0);
		}
		// do we've a valid patient ID?
		else if (is_numeric($_GET['delete'])
				&& $_GET['delete'] > 0)
		{
			$patient = DB::Call('get_patient_by_id', [$_GET['delete']], false);
			// none existing patient?
			if (empty($patient))
			{
				$_GET['message'] = 'Dieser Patient existiert nicht!';
			}
			else
			{
				DB::Call('delete_patient_by_id', [$patient['id']]);
				$_GET['message'] = 'Patient wurde gelöscht.';
			}
		}
		else
		{
			$_GET['message'] = 'Dieser Patient existiert nicht!';
		}
		
		header('location:/patient.php?show');
		exit(0);
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
		<form action="/patient.php?<?= isset($patient) ? "update=${patient['id']}" : 'create' ?>" method=post>
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
							<?php if (isset($addTreatment)): ?>
							<a href="/patient.php?cure=<?= $patient['id'] ?>" title="Patienten behandeln">+</a>
							<?php endif ?>
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