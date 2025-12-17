<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cloudlinker Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Cloudlinker API Test</h1>

        {{-- Connection Status --}}
        <div class="mb-8 p-4 rounded-lg {{ $connected ? 'bg-green-100 border border-green-400' : 'bg-red-100 border border-red-400' }}">
            <div class="flex items-center">
                @if($connected)
                    <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-green-800 font-semibold">Connected to Cloudlinker API</span>
                @else
                    <svg class="w-6 h-6 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span class="text-red-800 font-semibold">Not connected</span>
                @endif
            </div>
            @if($error)
                <p class="mt-2 text-red-700">{{ $error }}</p>
            @endif
        </div>

        {{-- Clients --}}
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">
                    Clients
                    <span class="ml-2 px-2 py-1 text-sm bg-blue-100 text-blue-800 rounded-full">{{ count($clients) }}</span>
                </h2>
            </div>
            <div class="p-6">
                @if(count($clients) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hostname</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Seen</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($clients as $client)
                                    <tr>
                                        <td class="px-4 py-3">
                                            @if($client->isOnline())
                                                <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">Online</span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-600 rounded-full">Offline</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $client->name }}</td>
                                        <td class="px-4 py-3 text-gray-500">{{ $client->hostname }}</td>
                                        <td class="px-4 py-3 text-xs text-gray-400 font-mono">{{ $client->id }}</td>
                                        <td class="px-4 py-3 text-gray-500">{{ $client->lastSeen }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500">No clients found. Make sure Cloudlinker software is installed and registered.</p>
                @endif
            </div>
        </div>

        {{-- Devices --}}
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">
                    Devices
                    <span class="ml-2 px-2 py-1 text-sm bg-purple-100 text-purple-800 rounded-full">{{ count($devices) }}</span>
                </h2>
            </div>
            <div class="p-6">
                @if(count($devices) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Driver</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client ID</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($devices as $device)
                                    <tr>
                                        <td class="px-4 py-3">
                                            @if($device->isPrinter())
                                                <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">Printer</span>
                                            @elseif($device->isScanner())
                                                <span class="px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full">Scanner</span>
                                            @elseif($device->isScale())
                                                <span class="px-2 py-1 text-xs font-semibold bg-indigo-100 text-indigo-800 rounded-full">Scale</span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full">{{ $device->type }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $device->name }}</td>
                                        <td class="px-4 py-3 text-gray-500">{{ $device->driver }}</td>
                                        <td class="px-4 py-3 text-xs text-gray-400 font-mono">{{ $device->id }}</td>
                                        <td class="px-4 py-3 text-xs text-gray-400 font-mono">{{ $device->clientId }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500">No devices found.</p>
                @endif
            </div>
        </div>

        {{-- Jobs --}}
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">
                    Jobs
                    <span class="ml-2 px-2 py-1 text-sm bg-orange-100 text-orange-800 rounded-full">{{ count($jobs) }}</span>
                </h2>
            </div>
            <div class="p-6">
                @if(count($jobs) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Device ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($jobs as $job)
                                    <tr>
                                        <td class="px-4 py-3">
                                            @if($job->isCompleted())
                                                <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">Completed</span>
                                            @elseif($job->isFailed())
                                                <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full">Failed</span>
                                            @elseif($job->isProcessing())
                                                <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">Processing</span>
                                            @elseif($job->isPending())
                                                <span class="px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full">Pending</span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full">{{ $job->status }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-gray-900">{{ $job->type }}</td>
                                        <td class="px-4 py-3 text-xs text-gray-400 font-mono">{{ $job->id }}</td>
                                        <td class="px-4 py-3 text-xs text-gray-400 font-mono">{{ $job->deviceId }}</td>
                                        <td class="px-4 py-3 text-gray-500">{{ $job->createdAt }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500">No jobs found.</p>
                @endif
            </div>
        </div>

        {{-- Quick Test Actions --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Quick Actions</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <p class="text-gray-600">Use these commands in <code class="bg-gray-100 px-2 py-1 rounded">php artisan tinker</code>:</p>

                    <div class="bg-gray-800 text-gray-100 p-4 rounded-lg font-mono text-sm overflow-x-auto">
                        <p class="text-green-400"># Test connection</p>
                        <p>Cloudlinker::test();</p>
                        <br>
                        <p class="text-green-400"># List all clients</p>
                        <p>Cloudlinker::clients()->all();</p>
                        <br>
                        <p class="text-green-400"># List devices for a client</p>
                        <p>Cloudlinker::devices()->all('client-uuid');</p>
                        <br>
                        <p class="text-green-400"># Create and launch a print job</p>
                        <p>Cloudlinker::jobs()->createAndLaunch([</p>
                        <p>    'device_id' => 'device-uuid',</p>
                        <p>    'type' => 'print',</p>
                        <p>    'source' => 'https://example.com/doc.pdf',</p>
                        <p>]);</p>
                    </div>
                </div>
            </div>
        </div>

        <footer class="mt-8 text-center text-gray-500 text-sm">
            Cloudlinker Laravel Client &bull; <a href="https://cloudlinker.eu" class="text-blue-500 hover:underline">cloudlinker.eu</a>
        </footer>
    </div>
</body>
</html>
