<?php

namespace Modules\Auth\Http\Controllers\Landlord;

use App\Http\Controllers\ApiController;
use Modules\Auth\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SettingsApiController extends ApiController
{
    protected SettingsService $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Get system settings
     */
    public function index(Request $request): JsonResponse
    {
        try 
        {
            $settings = $this->settingsService->getAllSettings();
            
            return $this->return(200, translate('Settings retrieved successfully'), [
                'settings' => $settings
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error retrieving settings: ' . $e->getMessage());
        }
    }

    /**
     * Update settings
     */
    public function update(Request $request): JsonResponse
    {
        try 
        {
            $request->validate([
                'settings' => 'required|array'
            ]);

            $settings = $this->settingsService->updateSettings($request->settings);
            
            return $this->return(200, translate('Settings updated successfully'), [
                'settings' => $settings
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error updating settings: ' . $e->getMessage());
        }
    }

    /**
     * Get security settings
     */
    public function securitySettings(Request $request): JsonResponse
    {
        try 
        {
            $securitySettings = $this->settingsService->getSecuritySettings();
            
            return $this->return(200, translate('Security settings retrieved successfully'), [
                'security_settings' => $securitySettings
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error retrieving security settings: ' . $e->getMessage());
        }
    }

    /**
     * Update security settings
     */
    public function updateSecurity(Request $request): JsonResponse
    {
        try 
        {
            $request->validate([
                'security_settings' => 'required|array'
            ]);

            $securitySettings = $this->settingsService->updateSecuritySettings($request->security_settings);
            
            return $this->return(200, translate('Security settings updated successfully'), [
                'security_settings' => $securitySettings
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error updating security settings: ' . $e->getMessage());
        }
    }

    /**
     * Get system settings
     */
    public function systemSettings(Request $request): JsonResponse
    {
        try 
        {
            $systemSettings = $this->settingsService->getSystemSettings();
            
            return $this->return(200, translate('System settings retrieved successfully'), [
                'system_settings' => $systemSettings
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error retrieving system settings: ' . $e->getMessage());
        }
    }

    /**
     * Update system settings
     */
    public function updateSystem(Request $request): JsonResponse
    {
        try 
        {
            $request->validate([
                'system_settings' => 'required|array'
            ]);

            $systemSettings = $this->settingsService->updateSystemSettings($request->system_settings);
            
            return $this->return(200, translate('System settings updated successfully'), [
                'system_settings' => $systemSettings
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error updating system settings: ' . $e->getMessage());
        }
    }

    /**
     * Reset to defaults
     */
    public function resetToDefaults(Request $request): JsonResponse
    {
        try 
        {
            $category = $request->get('category', 'all');
            $this->settingsService->resetToDefaults($category);
            
            return $this->return(200, translate('Settings reset to defaults successfully'));
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error resetting settings: ' . $e->getMessage());
        }
    }

    /**
     * Export settings
     */
    public function exportSettings(Request $request): JsonResponse
    {
        try 
        {
            $category = $request->get('category', 'all');
            $settingsData = $this->settingsService->exportSettings($category);
            
            return $this->return(200, translate('Settings exported successfully'), [
                'settings_export' => $settingsData
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error exporting settings: ' . $e->getMessage());
        }
    }

    /**
     * Import settings
     */
    public function importSettings(Request $request): JsonResponse
    {
        try 
        {
            $request->validate([
                'settings_data' => 'required|array'
            ]);

            $result = $this->settingsService->importSettings($request->settings_data);
            
            return $this->return(200, translate('Settings imported successfully'), [
                'import_result' => $result
            ]);
        } 
        catch (\Exception $e) 
        {
            return $this->return(500, 'Error importing settings: ' . $e->getMessage());
        }
    }
}
