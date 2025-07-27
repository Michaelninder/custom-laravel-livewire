<?php

namespace App\Livewire\Admin;

use App\Models\SiteSetting;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Validation\Rule;

#[Layout('components.layouts.admin')]
class SiteSettings extends Component
{
    public array $settings = [];

    public function mount(): void
    {
        $this->loadSettings();
    }

    protected function loadSettings(): void
    {
        $allSettings = SiteSetting::all();
        foreach ($allSettings as $setting) {
            $this->settings[$setting->key] = [
                'value' => $setting->value,
                'default_value' => $setting->default_value,
                'description' => $setting->description,
                'type' => $setting->type,
                'options' => $setting->data['options'] ?? null,
            ];
        }
    }

    public function updateSetting(string $key): void
    {
        $settingData = $this->settings[$key];
        $value = $settingData['value'];
        $rules = [];

        switch ($settingData['type']) {
            case 'string':
                $rules['settings.' . $key . '.value'] = 'nullable|string|max:255';
                break;
            case 'boolean':
                $rules['settings.' . $key . '.value'] = 'boolean';
                break;
            case 'enum':
                $rules['settings.' . $key . '.value'] = ['required', 'string', Rule::in($settingData['options'])];
                break;
            case 'json':
                $rules['settings.' . $key . '.value'] = ['nullable', 'json'];
                break;
            case 'text':
                $rules['settings.' . $key . '.value'] = 'nullable|string';
                break;
            default:
                $rules['settings.' . $key . '.value'] = 'nullable';
                break;
        }

        $this->validate($rules);

        $setting = SiteSetting::find($key);
        if ($setting) {
            $setting->value = $value;
            $setting->save();
            session()->flash('success', __('Site settings updated successfully.'));
        } else {
            session()->flash('error', __('Setting not found.'));
        }
    }

    public function resetSetting(string $key): void
    {
        $setting = SiteSetting::find($key);
        if ($setting) {
            $setting->value = null;
            $setting->save();
            $this->loadSettings();
            session()->flash('success', __('Setting reset to default.'));
        } else {
            session()->flash('error', __('Setting not found.'));
        }
    }

    public function render()
    {
        $breadcrumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Site Settings')],
        ];

        return view('livewire.admin.site-settings', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}