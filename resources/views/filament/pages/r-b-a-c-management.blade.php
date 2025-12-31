<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Role Descriptions -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-user-group class="w-5 h-5" />
                    Roles dalam Sistem
                </div>
            </x-slot>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($this->getRoleDescriptions() as $role => $info)
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="p-2 rounded-full bg-{{ $info['color'] }}-100 dark:bg-{{ $info['color'] }}-900">
                                <x-dynamic-component :component="$info['icon']" class="w-6 h-6 text-{{ $info['color'] }}-600" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $info['name'] }}</h3>
                                <p class="text-xs text-gray-500">{{ $role }}</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ $info['description'] }}</p>
                        <div class="space-y-1 text-xs">
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4 text-gray-400" />
                                <span class="text-gray-600 dark:text-gray-400">Login: <code class="text-primary-600">{{ $info['login_url'] }}</code></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-home class="w-4 h-4 text-gray-400" />
                                <span class="text-gray-600 dark:text-gray-400">Redirect: <code class="text-primary-600">{{ $info['default_redirect'] }}</code></span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        <!-- Access Control Matrix -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-table-cells class="w-5 h-5" />
                    Access Control Matrix
                </div>
            </x-slot>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-white">Resource</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-white">Description</th>
                            <th class="px-4 py-3 text-center font-semibold text-danger-600">Super Admin</th>
                            <th class="px-4 py-3 text-center font-semibold text-warning-600">Admin</th>
                            <th class="px-4 py-3 text-center font-semibold text-success-600">Karyawan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($this->getAccessMatrix() as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $row['resource'] }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">{{ $row['description'] }}</td>
                                <td class="px-4 py-3">
                                    @if(count($row['super_admin']) > 0)
                                        <div class="flex flex-wrap gap-1 justify-center">
                                            @foreach($row['super_admin'] as $permission)
                                                <span class="px-2 py-1 text-xs bg-danger-100 text-danger-700 dark:bg-danger-900 dark:text-danger-300 rounded">{{ $permission }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center text-gray-400">—</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if(count($row['admin']) > 0)
                                        <div class="flex flex-wrap gap-1 justify-center">
                                            @foreach($row['admin'] as $permission)
                                                <span class="px-2 py-1 text-xs bg-warning-100 text-warning-700 dark:bg-warning-900 dark:text-warning-300 rounded">{{ $permission }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center text-gray-400">—</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if(count($row['karyawan']) > 0)
                                        <div class="flex flex-wrap gap-1 justify-center">
                                            @foreach($row['karyawan'] as $permission)
                                                <span class="px-2 py-1 text-xs bg-success-100 text-success-700 dark:bg-success-900 dark:text-success-300 rounded">{{ $permission }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center text-gray-400">—</div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        <!-- Registered Policies -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-document-text class="w-5 h-5" />
                    Registered Policies
                </div>
            </x-slot>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($this->getRegisteredPolicies() as $policyName => $policyInfo)
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white">{{ $policyName }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Model: {{ $policyInfo['model'] }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300 rounded">{{ $policyInfo['scope'] }}</span>
                        </div>
                        <div class="flex flex-wrap gap-1 mt-2">
                            @foreach($policyInfo['methods'] as $method)
                                <code class="px-2 py-1 text-xs bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded text-gray-700 dark:text-gray-300">{{ $method }}()</code>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        <!-- Registered Gates -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-lock-closed class="w-5 h-5" />
                    Registered Gates
                </div>
            </x-slot>
            
            <div class="space-y-2">
                @foreach($this->getRegisteredGates() as $gate => $description)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <x-heroicon-o-key class="w-5 h-5 text-primary-600" />
                            <div>
                                <code class="text-sm font-semibold text-gray-900 dark:text-white">{{ $gate }}</code>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $description }}</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            @can($gate)
                                <span class="px-2 py-1 text-xs bg-success-100 text-success-700 dark:bg-success-900 dark:text-success-300 rounded flex items-center gap-1">
                                    <x-heroicon-o-check-circle class="w-3 h-3" />
                                    Granted
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded flex items-center gap-1">
                                    <x-heroicon-o-x-circle class="w-3 h-3" />
                                    Denied
                                </span>
                            @endcan
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        <!-- Security Features -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-shield-check class="w-5 h-5" />
                    Security Features
                </div>
            </x-slot>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-start gap-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                    <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" />
                    <div>
                        <h4 class="font-semibold text-green-900 dark:text-green-100">Data Isolation</h4>
                        <p class="text-sm text-green-700 dark:text-green-300">Automatic organization-based scoping menggunakan BelongsToOrganization trait</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                    <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" />
                    <div>
                        <h4 class="font-semibold text-green-900 dark:text-green-100">Cascade Protection</h4>
                        <p class="text-sm text-green-700 dark:text-green-300">Prevent delete jika resource memiliki dependencies</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                    <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" />
                    <div>
                        <h4 class="font-semibold text-green-900 dark:text-green-100">Self-Protection</h4>
                        <p class="text-sm text-green-700 dark:text-green-300">User tidak bisa edit/delete diri sendiri atau super admin</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                    <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" />
                    <div>
                        <h4 class="font-semibold text-green-900 dark:text-green-100">Role Separation</h4>
                        <p class="text-sm text-green-700 dark:text-green-300">Clear separation: Super Admin → Bisnis, Admin → Operasional, Karyawan → Self-service</p>
                    </div>
                </div>
            </div>
        </x-filament::section>

        <!-- Documentation -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-document-duplicate class="w-5 h-5" />
                    Dokumentasi
                </div>
            </x-slot>
            
            <div class="prose dark:prose-invert max-w-none">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Untuk dokumentasi lengkap tentang RBAC implementation, policy methods, gates usage, dan best practices, 
                    silakan baca file <code class="text-primary-600">RBAC.md</code> di root project.
                </p>
                
                <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-2">Quick Links:</h4>
                    <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
                        <li>• Policies: <code>app/Policies/</code></li>
                        <li>• Gates: <code>app/Providers/AppServiceProvider.php</code></li>
                        <li>• Trait: <code>app/Traits/BelongsToOrganization.php</code></li>
                        <li>• Documentation: <code>RBAC.md</code></li>
                    </ul>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
