@extends('layouts.app')

@section('title', 'Role Menu Permissions')
@section('page_title', 'Role & Sidebar Permissions')
@section('breadcrumb', 'Role Permissions')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Role & Sidebar Permissions</h1>
            <p class="text-sm text-slate-500">Configure which modules and sidebar tabs appear for Doctor and Receptionist roles</p>
        </div>
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-blue-50 text-blue-700 text-xs font-bold border border-blue-200">
            <i data-lucide="shield-check" class="w-4 h-4"></i> Super Admin Control
        </div>
    </div>

    <!-- Permissions Form -->
    <form action="{{ route('admin.roles.permissions.update') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        @csrf

        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Sidebar Navigation Visibility Matrix</h3>
                <p class="text-xs text-slate-400 mt-0.5">Check the boxes to allow access to specific modules for each role</p>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-[#0a2540] hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4 text-emerald-400"></i> Save Permissions
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-100 uppercase tracking-wider font-bold text-slate-500">
                        <th class="py-3.5 px-6">Module / Sidebar Navigation Item</th>
                        <th class="py-3.5 px-6 text-center w-40">
                            <span class="inline-block px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 font-bold border border-blue-200">
                                🩺 Doctor Role
                            </span>
                        </th>
                        <th class="py-3.5 px-6 text-center w-48">
                            <span class="inline-block px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-bold border border-emerald-200">
                                💼 Receptionist / Staff
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($menuDefinitions as $menuKey => $meta)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                                        <i data-lucide="{{ $meta['icon'] }}" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-900 text-sm">{{ $meta['title'] }}</h4>
                                        <p class="text-slate-400 text-[11px]">{{ $meta['description'] }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Doctor Checkbox -->
                            <td class="py-4 px-6 text-center">
                                <label class="inline-flex items-center justify-center cursor-pointer p-2">
                                    <input type="checkbox" name="permissions[doctor][{{ $menuKey }}]" value="1"
                                           {{ ($permissions['doctor'][$menuKey] ?? false) ? 'checked' : '' }}
                                           class="w-5 h-5 rounded-md border-slate-300 text-[#0a2540] focus:ring-primary">
                                </label>
                            </td>

                            <!-- Receptionist Checkbox -->
                            <td class="py-4 px-6 text-center">
                                <label class="inline-flex items-center justify-center cursor-pointer p-2">
                                    <input type="checkbox" name="permissions[receptionist][{{ $menuKey }}]" value="1"
                                           {{ ($permissions['receptionist'][$menuKey] ?? false) ? 'checked' : '' }}
                                           class="w-5 h-5 rounded-md border-slate-300 text-[#0a2540] focus:ring-primary">
                                </label>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
            <span class="text-xs text-slate-400">
                Note: Super Admin always retains full, unrestricted access to all modules and configurations.
            </span>
            <button type="submit" class="px-6 py-2.5 bg-[#0a2540] hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i> Apply & Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
