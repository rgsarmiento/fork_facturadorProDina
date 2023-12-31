<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Storage;
use Artisan;

class UpdateController extends Controller
{
    public function index()
    {
        return view('system.update.index');
    }

    public function version()
    {
        $id = new Process('git show HEAD~0 --pretty=format:"%h" --no-patch');
        $id->run();
        $res_id = $id->getOutput();
        $tag = new Process('git tag | sort -V | tail -1');
        $tag->run();
        $res_tag = $tag->getOutput();
        return json_encode($res_tag.' - '.$res_id);
    }

    public function branch()
    {
        $process = new Process('git rev-parse --abbrev-ref HEAD');
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        $output = $process->getOutput();
        return json_encode($output);
    }

    public function pull($branch)
    {
        $process = new Process('git pull origin '.$branch);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        $output = $process->getOutput();

        $fetch = new Process('git fetch');
        $fetch->run();

        return json_encode($output);
    }

    public function artisanMigrate()
    {
        $output = Artisan::call('migrate');
        return json_encode($output);
    }

    public function artisanTenancyMigrate()
    {
        $output = Artisan::call('tenancy:migrate');
        return json_encode($output);
    }

    public function artisanClear()
    {
        $output = Artisan::call('config:cache');
        return json_encode($output);
    }

    public function composerInstall()
    {
        $process = new Process(system('composer dumpautoload -d '. base_path()));
        $process->run();
        $output = $process->getOutput();

        $chmod = new Process('chmod -R 777 ../vendor/mpdf/mpdf');
        $chmod->run();

        return json_encode($output);
    }

    public function keygen()
    {
        //genero ssh
        // $process = new Process(['chmod +x ../script-ssh.sh','sh ../script-ssh.sh']);
        // $process->run();
        // if (!$process->isSuccessful()) {
        //     throw new ProcessFailedException($process);
        // }
        // $output = $process->getOutput();

        //genero ssh sin validar
        //ssh-keygen -t rsa -q -P "" -f ../id_rsa


        // copio ssh a contenedor
        //docker cp archivo.txt facturadorpro31_fpm1_1:/root/.ssh/

        //eliminar la clave creada para evitar conflictos con el pull
        // rm ../id_*

        /* alternativa
        $process = new Process('sh /folder_name/file_name.sh');
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        echo $process->getOutput();
        */



        // return json_encode($output);
    }
}
