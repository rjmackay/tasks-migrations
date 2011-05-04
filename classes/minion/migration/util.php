<?php defined('SYSPATH') or die('No direct script access.');


/**
 * Provides a set of utility functions for managing migrations
 *
 * @author Matt Button <matthew@sigswitch.com>
 */
class Minion_Migration_Util {

	/**
	 * Parses a set of files generated by Kohana::find_files and compiles it 
	 * down into an array of migrations
	 *
	 * @param  array Available files
	 * @return array Available Migrations
	 */
	public static function compile_migrations_from_files(array $files)
	{
		$migrations = array();

		foreach ($files as $file => $path)
		{
			// If this is a directory we're dealing with
			if (is_array($path))
			{
				$migrations += Minion_Migration_Util::compile_migrations_from_files($path);
			}
			else
			{
				$migration = Minion_Migration_Util::get_migration_from_filename($file);

				$migrations[$migration['group'].':'.$migration['timestamp']] = $migration;
			}
		}

		return $migrations;
	}

	/**
	 * Extracts information about a migration from its filename.
	 *
	 * Returns an array like:
	 *
	 *     array(
	 *        'group'    => 'mygroup',
	 *        'timestamp'   => '1293214439',
	 *        'description' => 'initial-setup',
	 *        'id'          => 'mygroup:1293214439'
	 *     );
	 *
	 * @param  string The migration's filename
	 * @return array  Array of components about the migration
	 */
	public static function get_migration_from_filename($file)
	{
		$migration = array();

		// Get rid of the file's "migrations/" prefix, the file extension and then 
		// the filename itself.  The "group" is essentially a slash delimited 
		// path from the migrations folder to the migration file
		$migration['group'] = dirname(substr($file, 11, -strlen(EXT)));

		list($migration['timestamp'], $migration['description']) 
			= explode('_', basename($file, EXT), 2);

		$migration['id'] = $migration['group'].':'.$migration['timestamp'];

		return $migration;
	}

	/**
	 * Gets a migration file from its timestamp, description and group
	 *
	 * @param  integer|array The migration's ID or an array of timestamp, description
	 * @param  string        The migration group
	 * @return string        Path to the migration file
	 */
	public static function get_filename_from_migration(array $migration)
	{
		$group  = $migration['group'];
		$migration = $migration['timestamp'].'_'.$migration['description'];

		$group = ( ! empty($group)) ? (rtrim($group, '/').'/') : '';

		return $group.$migration.EXT;
	}

	/**
	 * Allows you to work out the class name from either an array of migration 
	 * info, or from a migration id
	 *
	 * @param  string|array The migration's ID or array of migration data
	 * @return string       The migration class name
	 */
	public static function get_class_from_migration($migration)
	{
		$log = Log::instance();
		ob_start();
		var_dump($migration);
		$log->add(Log::INFO, ob_get_clean());
		if (is_string($migration))
		{
			$migration = str_replace(array(':', '/'), ' ', $migration);
		}
		else
		{
			$migration = str_replace('/', ' ', $migration['group']).'_'.$migration['timestamp'];
		}

		return 'Migration_'.str_replace(array(' ', '-'), '_', ucwords($migration));
	}
}
