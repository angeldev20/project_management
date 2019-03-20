<?php defined("BASEPATH") or exit("No direct script access allowed");

use Phinx\Migration\AbstractMigration;
use Phinx\Console\PhinxApplication;
use Phinx\Config\Config as PConfig;
use Phinx\Wrapper\TextWrapper;
use Symfony\Component\Yaml\Yaml;

use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class Migrate extends CI_Controller{

    public function index(){

        if ($_SESSION['accountUrlPrefix'] != 'spera' && $_SESSION['accountUrlPrefix'] != 'damon' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1' && $_SESSION['accountUrlPrefix'] != 'test') {
            echo "Sorry you are not authorized to use this api";
            return;
        }

        $this->load->library("migration");

        $primaryDatabase = $this->load->database('primary', TRUE);

        

        $sql = "SELECT count(*) as cnt FROM accounts";
        $count = $primaryDatabase->query($sql);
        $count = $count->result_array();
        $count = $count[0]['cnt'];
        // yaml_emit_file("accountmigrate.yml", $total,YAML_UTF8_ENCODING);

        $tableList = [
            'project_chats',
            'slack_linked_channels',
            'slack_links'
        ];

        for ($start=0; $start < $count; $start+= 50) {

            $path = array("migrations" => "%%PHINX_CONFIG_DIR%%/db/migrations/seeddb",
                "seeds" => "%%PHINX_CONFIG_DIR%%/db/seeddb/seeds");

            $environments = array("default_migration_table"=>"phinxlog",
                "default_database"=> "development",
            );

            $sql = "SELECT * FROM accounts limit $start, 50";
            $accounts = $primaryDatabase->query($sql,[]);
            
            foreach($accounts->result_array() as $row){

                $prefix = $row['accountUrlPrefix'];
                $db_name = $row['db_database'];
                $db_host = $row['db_hostname'];

                $db_config = array("adapter"=>"mysql",
                    "host"=>$db_host,
                    "name"=>$db_name,
                    "user"=>(ENVIRONMENT == 'production') ? "platform" : "platform_".ENVIRONMENT,
                    "pass"=> (ENVIRONMENT == 'production') ? 'J(NsVHrVj[!_,8+`85t&' : '4ZSyw@tx/7dw+vW[',
                    "port"=>"3306",
                    "charset"=>"utf8",
                );
                
                $environments[$prefix] = $db_config;
            }



            $total = array(
                "paths" => $path,
                "environments" => $environments,
                "version_order" => "creation"
            );
            


            $yaml = Yaml::dump($total,3);

            $yaml = str_replace("'%%PHINX_CONFIG_DIR%%/db/seeddb/seeds'", "%%PHINX_CONFIG_DIR%%/db/seeddb/seeds", $yaml);
            $yaml = str_replace("'%%PHINX_CONFIG_DIR%%/db/migrations/seeddb'", "%%PHINX_CONFIG_DIR%%/db/migrations/seeddb", $yaml);

            file_put_contents('accountmigrate.yml', $yaml);

            $app = new PhinxApplication();
            $app->setAutoExit(false);


            foreach($accounts->result_array() as $row){

                $prefix = $row['accountUrlPrefix'];
                $db_name = $row['db_database'];

                //     $primaryDatabase->query("DROP TABLE IF EXISTS ".$db_name.".`$table`");
                //     echo "DROP TABLE IF EXISTS ".$db_name.".`$table`"."<br>";
                // }

                $sql = "CREATE TABLE IF NOT EXISTS $db_name.`phinxlog` (
                    `version` bigint(20) NOT NULL,
                    `migration_name` varchar(100) DEFAULT NULL,
                    `start_time` timestamp NULL DEFAULT NULL,
                    `end_time` timestamp NULL DEFAULT NULL,
                    `breakpoint` tinyint(1) NOT NULL DEFAULT '0',
                    PRIMARY KEY (`version`)
                )";
                $primaryDatabase->query($sql);
                
                $sql = "UPDATE $db_name.`modules` SET $db_name.`modules`.`link` = 'projects' WHERE $db_name.`modules`.`type` = 'client' AND $db_name.`modules`.`link` = 'cprojects'";
                $primaryDatabase->query($sql);

                $sql = "UPDATE $db_name.`users` SET $db_name.`users`.`access` = CONCAT($db_name.`users`.`access`,',108') WHERE $db_name.`users`.`access` NOT LIKE '%,108%' ";
                $primaryDatabase->query($sql);

                // $reslt = $app->run(new StringInput("migrate -c accountmigrate.yml -e $prefix"), new NullOutput());

                // if($result == 0){
                //     echo ($db_name." is successfully migrated. <br>");
                // }else{
                //     echo ($db_name." is not migrated. <br>");
                // }
                try {
                    $phinxApp = new \Phinx\Console\PhinxApplication();
                    $phinxTextWrapper = new \Phinx\Wrapper\TextWrapper($phinxApp);

                    $phinxTextWrapper->setOption('configuration', 'accountmigrate.yml');
                    $phinxTextWrapper->setOption('parser', 'YAML');
                    $phinxTextWrapper->setOption('environment', $prefix);

                    $log = $phinxTextWrapper->getMigrate();
                    
                    echo("$db_name => Migrated <br>");
                    print_r($log);
                    echo("<br>-------------------------------------------<br>");
                } catch (Exception $e) {
                    echo("$db_name => Fatal error<br>");
                    // print_r($e);
                }
            } 
        }
        
	}

    
    public function checktable(){
        $primaryDatabase = $this->load->database('primary', TRUE);

        $sql = "SELECT * FROM accounts";
            $accounts = $primaryDatabase->query($sql,[]);
        
        $tableList = [
            'project_chats',
            'slack_linked_channels',
            'slack_links'
        ];

        foreach($accounts->result_array() as $row){

            $db_name = $row['db_database'];
            $db_host = $row['db_hostname'];
            
            foreach ($tableList as $table) {
                $primaryDatabase->query("DROP TABLE IF EXISTS ".$db_name.".`$table`");
            }
            $sql = "TRUNCATE $db_name.phinxlog";
            $result = $primaryDatabase->query($sql);

            // echo("$db_name<br>");
            // print_r($result->result_array());
            
            echo("<br>-------------------------------------<br>");
        }

        // $sql = "SELECT * from daniel_development.slack_links";
        // $result = $primaryDatabase->query($sql);
        // print_r($result->result_array());
    }
    // public function test()
    // {
    //     $app = new PhinxApplication();
    //     $app->setAutoExit(false);
    //     $this->load->library("migration");

    //     $primaryDatabase = $this->load->database('primary', TRUE);

    //     $tableList = [
    //         'project_chats',
    //         'slack_linked_channels',
    //         'slack_links'
    //     ];

    //     $prefix = 'test7';
    //     $db_name = 'test7_production';
    //     foreach ($tableList as $table) {
    //         $primaryDatabase->query("DROP TABLE IF EXISTS ".$db_name.".`$table`");
    //         echo "DROP TABLE IF EXISTS ".$db_name.".`$table`"."<br>";
    //     }

    //     $sql = "CREATE TABLE IF NOT EXISTS $db_name.`phinxlog` (
    //         `version` bigint(20) NOT NULL,
    //         `migration_name` varchar(100) DEFAULT NULL,
    //         `start_time` timestamp NULL DEFAULT NULL,
    //         `end_time` timestamp NULL DEFAULT NULL,
    //         `breakpoint` tinyint(1) NOT NULL DEFAULT '0',
    //         PRIMARY KEY (`version`)
    //     )";
    //     $primaryDatabase->query($sql);
        
    //     $reslt = $app->run(new StringInput("migrate -c accountmigrate.yml -e $prefix"), new NullOutput());
    //     echo ($db_name." is migrated. <br>");
    // }
}