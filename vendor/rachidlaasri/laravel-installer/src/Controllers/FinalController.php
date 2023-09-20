<?php

namespace RachidLaasri\LaravelInstaller\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\URL;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;
use RachidLaasri\LaravelInstaller\Events\LaravelInstallerFinished;
use RachidLaasri\LaravelInstaller\Helpers\EnvironmentManager;
use RachidLaasri\LaravelInstaller\Helpers\FinalInstallManager;
use RachidLaasri\LaravelInstaller\Helpers\InstalledFileManager;

class FinalController extends Controller
{
    /**
     * Update installed file and display finished view.
     *
     * @param \RachidLaasri\LaravelInstaller\Helpers\InstalledFileManager $fileManager
     * @param \RachidLaasri\LaravelInstaller\Helpers\FinalInstallManager $finalInstall
     * @param \RachidLaasri\LaravelInstaller\Helpers\EnvironmentManager $environment
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function finish(InstalledFileManager $fileManager, FinalInstallManager $finalInstall, EnvironmentManager $environment){
        $finalMessages = $finalInstall->runFinal();
        $finalStatusMessage = $fileManager->update();
        $finalEnvFile = $environment->getEnvContent();

        event(new LaravelInstallerFinished);

        return view('vendor.installer.finished', compact('finalMessages', 'finalStatusMessage', 'finalEnvFile'));
    }

    public function adminSetup(){
        return view('vendor.installer.admin-setup');
    }

    public function saveAdminSetup(){
        $userData = \Illuminate\Support\Facades\Request::validate([
            'first_name' => ['required', 'max:100'],
            'last_name' => ['required', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'password' => ['required', 'max:100'],
        ]);
        $userData['role_id'] = 1;
        $user = User::create($userData);

        $env = DotenvEditor::load();
        $env->setKey('APP_INSTALLED', 'true');
        $env->setKey('DB_SETUP', 'true');
        $env->save();
        Artisan::call('config:cache');

        return redirect()->route('LaravelInstaller::final')->with(['message' => 'The application installed successfully!']);
    }
}
