@extends('layouts.app')

@section('title', 'Document Management System (DMS)')

@section('content')
<div class="space-y-6" x-data="{ uploadModal: false, ownerType: 'project' }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Document Management System</h1>
            <p class="text-sm text-gray-500 mt-1">Encrypted repository for talent NDAs, client agreements, shoot briefs, and claim receipts</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="uploadModal = true" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Upload Document
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <form method="GET" action="{{ route('admin.documents.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by file name..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Category</label>
                <div class="flex gap-2">
                    <select name="category" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $cat)) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg">Filter</button>
                    @if(request()->hasAny(['search', 'category']))
                        <a href="{{ route('admin.documents.index') }}" class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium rounded-lg flex items-center">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Documents Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase font-semibold text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Document Name</th>
                        <th class="px-4 py-3 text-left">Category</th>
                        <th class="px-4 py-3 text-left">Linked Entity</th>
                        <th class="px-4 py-3 text-left">Version</th>
                        <th class="px-4 py-3 text-left">Size</th>
                        <th class="px-4 py-3 text-left">Uploaded By</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($documents as $doc)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-semibold text-gray-900">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    <span class="truncate max-w-xs">{{ $doc->original_name }}</span>
                                    @if($doc->is_confidential)
                                        <span class="px-1.5 py-0.5 rounded text-[9px] bg-red-100 text-red-700 font-bold uppercase">Confidential</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 capitalize">
                                    {{ str_replace('_', ' ', $doc->category) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                @if($doc->documentable instanceof \App\Models\Project)
                                    <span class="font-mono text-indigo-600">Project:</span> {{ $doc->documentable->project_name }}
                                @elseif($doc->documentable instanceof \App\Models\Contract)
                                    <span class="font-mono text-purple-600">Contract:</span> {{ $doc->documentable->contract_no }}
                                @else
                                    <span class="text-gray-400">Direct</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs font-mono text-gray-600">v{{ $doc->version ?? '1.0' }}</td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ round(($doc->file_size ?? 0) / 1024) }} KB</td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                <div>{{ $doc->uploader?->name ?? 'System' }}</div>
                                <div class="text-gray-400 text-[11px]">{{ $doc->created_at?->format('d M Y') }}</div>
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('admin.documents.download', $doc) }}" class="inline-flex items-center px-2.5 py-1 rounded bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-xs font-semibold">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Download
                                </a>
                                <form method="POST" action="{{ route('admin.documents.destroy', $doc) }}" onsubmit="return confirm('Permanently delete document?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">No documents found in repository.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $documents->links() }}
        </div>
    </div>

    <!-- UPLOAD MODAL -->
    <div x-show="uploadModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="uploadModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full p-6">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Upload to Document Repository</h3>
                    <button @click="uploadModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <form method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Select File *</label>
                        <input type="file" name="file" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <span class="text-[11px] text-gray-400">PDF, Word, Excel, Images (Max 20MB)</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Category *</label>
                        <select name="category" required class="w-full px-3 py-2 border rounded-lg text-sm">
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ ucfirst(str_replace('_', ' ', $cat)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Attach To</label>
                        <div class="grid grid-cols-2 gap-3 mb-2">
                            <label class="flex items-center gap-2 text-xs font-semibold text-gray-700 cursor-pointer">
                                <input type="radio" name="owner_type" value="project" x-model="ownerType" checked class="text-indigo-600">
                                Project
                            </label>
                            <label class="flex items-center gap-2 text-xs font-semibold text-gray-700 cursor-pointer">
                                <input type="radio" name="owner_type" value="contract" x-model="ownerType" class="text-indigo-600">
                                Contract
                            </label>
                        </div>

                        <!-- Project Selector -->
                        <div x-show="ownerType === 'project'" x-cloak>
                            <select name="owner_id" :disabled="ownerType !== 'project'" class="w-full px-3 py-2 border rounded-lg text-sm">
                                @foreach($projects as $proj)
                                    <option value="{{ $proj->id }}">{{ $proj->project_name }} ({{ $proj->project_code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Contract Selector -->
                        <div x-show="ownerType === 'contract'" x-cloak style="display: none;">
                            <select name="owner_id" :disabled="ownerType !== 'contract'" class="w-full px-3 py-2 border rounded-lg text-sm">
                                @foreach($contracts as $cnt)
                                    <option value="{{ $cnt->id }}">{{ $cnt->contract_no }} - {{ $cnt->contract_title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                        <label class="flex items-center gap-2 text-xs font-bold text-gray-900 cursor-pointer">
                            <input type="checkbox" name="is_confidential" value="1" checked class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                            Confidential Document (Requires clearance to view)
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="uploadModal = false" class="px-4 py-2 border rounded-lg text-sm">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold">Upload File</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

