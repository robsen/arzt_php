<?php

	class DB
	{
		private static $username = 'arzt';
		private static $password = '1234';
		private static $database = null;
		
		private function Connect()
		{
			if (self::$database === null)
			{
				self::$database =
					new PDO(
						'mysql:host=localhost;dbname=arzt;',
						self::$username,
						self::$password
					);
			}
		}
		
		public static function Call($storedProcedure, array $parameters = null, $allowSingleResultSet = true)
		{
			self::Connect();
			
			$numberOfParameters = $parameters === null ? 0 : count($parameters);
			$lastParameter = $numberOfParameters - 1; // index starts at 0 therefore -1
			
			// apend parameters to procedure call
			$sql = "CALL $storedProcedure(";
			for ($i = 0; $i < $numberOfParameters; $i++)
			{
				$sql .= ($i === $lastParameter) ? '?' : '?,';
			}
			$sql .= ');';
			
			$query = self::$database->prepare($sql);
			$query->execute($parameters);
			$resultSet = $query->fetchAll(PDO::FETCH_ASSOC);
			
			// resultset with single entry?
			if ($allowSingleResultSet === false
				&& count($resultSet) === 1)
			{
				// return single result instead of resultset
				return $resultSet[0];
			}
			return $resultSet;
		}
	}