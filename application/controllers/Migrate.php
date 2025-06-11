<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->helper(['url', 'form']);
		$this->load->library('migration');
		$this->load->database();
	}

	public function index(): void
	{
		ob_start();

		echo "<h1>🚀 Executando Todas as Migrations</h1>";

		$this->_show_navigation_links();

		$migrations = $this->_get_migration_files();
		$current_version = $this->_get_current_version();

		if (empty($migrations)) {
			echo "<p style='color: orange;'>⚠️ Nenhuma migration encontrada na pasta application/migrations/</p>";
			return;
		}

		$pending_migrations = array_filter($migrations, function($timestamp) use ($current_version) {
			return $timestamp > $current_version;
		}, ARRAY_FILTER_USE_KEY);

		if (empty($pending_migrations)) {
			echo "<p style='color: blue;'>ℹ️ Todas as migrations já foram executadas!</p>";
		} else {
			echo "<p style='color: blue;'>📦 Encontradas " . count($pending_migrations) . " migration(s) pendente(s)</p>";

			echo "<h3>Migrations que serão executadas:</h3>";
			echo "<ul>";
			foreach ($pending_migrations as $timestamp => $file) {
				echo "<li>{$timestamp} - {$file}</li>";
			}
			echo "</ul>";
		}

		echo "<h3>Executando...</h3>";

		if ($this->migration->latest() === FALSE) {
			echo "<p style='color: red;'>❌ Erro: " . $this->migration->error_string() . "</p>";
		} else {
			echo "<p style='color: green;'>✅ Migrations executadas com sucesso!</p>";
		}

		$this->_show_status();

		ob_end_flush();
	}

	public function version($timestamp = null): void
	{
		if ($timestamp === null) {
			echo "<h1>❌ Erro: Timestamp não especificado</h1>";
			echo "<p>Formato esperado: YYYYMMDDHHIISS (ex: 20250610120000)</p>";
			$this->_show_navigation_links();
			return;
		}

		if (!preg_match('/^\d{14}$/', $timestamp)) {
			echo "<h1>❌ Erro: Formato de timestamp inválido</h1>";
			echo "<p>Use o formato: YYYYMMDDHHIISS</p>";
			$this->_show_navigation_links();
			return;
		}

		echo "<h1>🎯 Migrando para timestamp {$timestamp}</h1>";
		$this->_show_navigation_links();

		$current_version = $this->_get_current_version();
		echo "<p><strong>Versão atual:</strong> {$current_version}</p>";
		echo "<p><strong>Versão alvo:</strong> {$timestamp}</p>";

		if ($timestamp > $current_version) {
			echo "<p style='color: blue;'>⬆️ Executando migrations...</p>";
		} elseif ($timestamp < $current_version) {
			echo "<p style='color: orange;'>⬇️ Fazendo rollback...</p>";
		} else {
			echo "<p style='color: blue;'>ℹ️ Já está na versão solicitada</p>";
		}

		if ($this->migration->version($timestamp) === FALSE) {
			echo "<p style='color: red;'>❌ Erro: " . $this->migration->error_string() . "</p>";
		} else {
			echo "<p style='color: green;'>✅ Migração para timestamp {$timestamp} executada com sucesso!</p>";
		}

		$this->_show_status();
	}

	private function _show_navigation_links(): void
	{
		echo "<div style='background-color: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
		echo "<h3>🧭 Comandos Disponíveis:</h3>";
		echo "<div style='display: flex; flex-wrap: wrap; gap: 10px;'>";
		echo "<a href='/migrate/status' style='padding: 5px 10px; background-color: #2196f3; color: white; text-decoration: none; border-radius: 3px;'>📊 Status</a>";
		echo "<a href='/migrate' style='padding: 5px 10px; background-color: #4caf50; color: white; text-decoration: none; border-radius: 3px;'>🚀 Executar Todas</a>";
		echo "<a href='/migrate/next' style='padding: 5px 10px; background-color: #ff9800; color: white; text-decoration: none; border-radius: 3px;'>⏭️ Próxima</a>";
		echo "<a href='/migrate/prev' style='padding: 5px 10px; background-color: #f44336; color: white; text-decoration: none; border-radius: 3px;'>⏮️ Anterior</a>";
		echo "<a href='/migrate/reset' style='padding: 5px 10px; background-color: #9c27b0; color: white; text-decoration: none; border-radius: 3px;'>🔄 Reset</a>";
		echo "</div>";
		echo "<p style='margin-top: 10px; font-size: 0.9em;'><strong>Criar:</strong> /migrate/create/nome_da_migration | <strong>Versão específica:</strong> /migrate/version/20250610120000</p>";
		echo "</div>";
	}

	public function reset(): void
	{
		echo "<h1>Resetando todas as migrations</h1>";

		if ($this->migration->version(0) === FALSE) {
			echo "<p style='color: red;'>Erro: " . $this->migration->error_string() . "</p>";
		} else {
			echo "<p style='color: green;'>Todas as migrations foram resetadas!</p>";
		}

		$this->_show_status();
	}

	private function _show_status(): void
	{
		echo "<hr>";
		echo "<h2>Status das Migrations</h2>";

		$current_version = 0;

		try {
			if (!isset($this->db)) {
				$this->load->database();
			}

			$migration_table = $this->config->item('migration_table') ?: 'migrations';

			$tables = $this->db->list_tables();
			$table_exists = in_array($migration_table, $tables);

			if ($table_exists) {
				$query = $this->db->select('version')->from($migration_table)->limit(1)->get();

				if ($query && $query->num_rows() > 0) {
					$row = $query->row();
					$current_version = isset($row->version) ? $row->version : 0;
				}
			} else {
				echo "<p style='color: orange;'>Tabela de migrations ainda não foi criada.</p>";
			}
		} catch (Exception $e) {
			echo "<p style='color: red;'>Erro ao verificar migrations: " . $e->getMessage() . "</p>";
		}

		echo "<p><strong>Versão atual no banco:</strong> " . ($current_version ?: 'Nenhuma') . "</p>";

		echo "<h3>Migrations Disponíveis:</h3>";

		$migrations = $this->_get_migration_files();

		if (empty($migrations)) {
			echo "<p style='color: orange;'>Nenhuma migration encontrada na pasta application/migrations/</p>";
			echo "<p>Crie suas migrations primeiro!</p>";
		} else {
			echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse;'>";
			echo "<tr style='background-color: #f0f0f0;'><th>Timestamp</th><th>Arquivo</th><th>Status</th></tr>";

			foreach ($migrations as $timestamp => $file) {
				$status = ($timestamp <= $current_version) ? '✅ Executada' : '⏳ Pendente';
				$style = ($timestamp <= $current_version) ? 'color: green;' : 'color: orange;';
				echo "<tr><td>{$timestamp}</td><td>{$file}</td><td style='{$style}'>{$status}</td></tr>";
			}

			echo "</table>";
		}

		echo "<h3>Informações:</h3>";
		echo "<ul>";
		echo "<li><strong>Pasta de migrations:</strong> " . APPPATH . "migrations/</li>";
		echo "<li><strong>Tipo de migration:</strong> " . ($this->config->item('migration_type') ?: 'sequential') . "</li>";
		echo "<li><strong>Tabela de controle:</strong> " . ($this->config->item('migration_table') ?: 'migrations') . "</li>";
		echo "<li><strong>Total de arquivos:</strong> " . count($migrations) . "</li>";
		echo "</ul>";

		echo "<h3>Comandos Úteis:</h3>";
		echo "<ul>";
		echo "<li><a href='/migrate'>Executar todas as migrations</a></li>";
		echo "<li><a href='/migrate/reset'>Resetar todas as migrations</a></li>";
		echo "</ul>";
	}

	private function _get_current_version(): int
	{
		$current_version = 0;

		try {
			if (!isset($this->db)) {
				$this->load->database();
			}

			$migration_table = $this->config->item('migration_table') ?: 'migrations';
			$tables = $this->db->list_tables();

			if (in_array($migration_table, $tables)) {
				$query = $this->db->select('version')->from($migration_table)->limit(1)->get();

				if ($query && $query->num_rows() > 0) {
					$row = $query->row();
					$current_version = isset($row->version) ? $row->version : 0;
				}
			}
		} catch (Exception $e) {
			log_message('error', 'Erro ao verificar versão das migrations: ' . $e->getMessage());
		}

		return $current_version;
	}
	private function _get_migration_files(): array
	{
		$migrations = [];
		$migration_files = glob(APPPATH . 'migrations/*.php');

		foreach ($migration_files as $file) {
			$filename = basename($file);
			if (preg_match('/^(\d{14})_(.+)\.php$/', $filename, $matches)) {
				$migrations[$matches[1]] = $filename;
			}
		}

		ksort($migrations);
		return $migrations;
	}
}
