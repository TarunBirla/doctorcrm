@extends('layouts.app')

@section('title', 'Audit Trail & Activity Logs')
@section('breadcrumb', 'Security / Audit Logs')
@section('page_title', 'System Audit Trail & Security Logs')

@section('content')
<div class="space-y-6">

    <!-- FILTER BAR -->
    <div class="card-custom p-5 bg-white">
        <form action="{{ route('audit-logs.index') }}" method="GET" class="flex flex-wrap items-center justify-between gap-4 text-xs">
            <div class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search action, description, or user..." 
                       class="w-72 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:border-blue-500">
                <select name="role" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                    <option value="">All Roles</option>
                    <option value="super_admin" {{ $role === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="doctor" {{ $role === 'doctor' ? 'selected' : '' }}>Doctor</option>
                    <option value="receptionist" {{ $role === 'receptionist' ? 'selected' : '' }}>Receptionist</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-navy-900 text-white font-bold rounded-xl hover:bg-navy-800 transition">Filter</button>
                <a href="{{ route('audit-logs.index') }}" class="px-3 py-2 border rounded-xl text-slate-600 hover:bg-slate-50 font-semibold">Reset</a>
            </div>
            <span class="text-xs text-slate-400">Total {{ $logs->total() }} recorded audit events</span>
        </form>
    </div>

    <!-- LOGS TABLE -->
    <div class="card-custom bg-white overflow-hidden">
        <div class="overflow-x-auto text-xs">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    <tr class="border-b border-slate-100">
                        <th class="p-3.5 pl-5">Timestamp</th>
                        <th class="p-3.5">User</th>
                        <th class="p-3.5">Role</th>
                        <th class="p-3.5">Action Event</th>
                        <th class="p-3.5">Entity Details</th>
                        <th class="p-3.5">Description</th>
                        <th class="p-3.5 pr-5">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50">
                            <td class="p-3.5 pl-5 font-mono text-slate-500 whitespace-nowrap">
                                {{ $log->created_at->format('d M Y, H:i:s') }}
                            </td>
                            <td class="p-3.5 font-bold text-slate-900">{{ $log->user_name ?? 'System' }}</td>
                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase 
                                    @if($log->role === 'super_admin') bg-purple-100 text-purple-800
                                    @elseif($log->role === 'doctor') bg-blue-100 text-blue-800
                                    @else bg-slate-100 text-slate-700 @endif">
                                    {{ str_replace('_', ' ', $log->role ?? 'admin') }}
                                </span>
                            </td>
                            <td class="p-3.5 font-bold text-slate-800">{{ $log->action }}</td>
                            <td class="p-3.5 font-mono text-[11px] text-blue-700">
                                {{ $log->entity_type ? $log->entity_type . ' #' . $log->entity_id : '-' }}
                            </td>
                            <td class="p-3.5 text-slate-600 max-w-sm">{{ $log->description }}</td>
                            <td class="p-3.5 pr-5 font-mono text-slate-400 text-[11px]">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-12 text-center text-slate-400">No audit events logged.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
