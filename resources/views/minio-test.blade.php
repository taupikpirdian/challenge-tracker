<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Minio Upload Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Minio Upload Test</h1>
            <p class="text-gray-600">Test file upload to Minio S3-compatible storage</p>
        </div>

        <!-- Test Connection Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Test Connection</h2>
            <button id="testConnection" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Test Minio Connection
            </button>
            <div id="connectionResult" class="mt-4 hidden"></div>
        </div>

        <!-- Upload Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Upload File</h2>
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="file">
                        Choose a file (max 10MB)
                    </label>
                    <input class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100"
                        id="file" type="file" name="file" required accept="image/*,.pdf,.doc,.docx,.txt">
                </div>
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Upload to Minio
                </button>
            </form>
            <div id="uploadResult" class="mt-4 hidden"></div>
        </div>

        <!-- List Files Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Uploaded Files</h2>
            <button id="listFiles" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                List All Files
            </button>
            <div id="filesResult" class="mt-4 hidden"></div>
        </div>

        <!-- Uploaded Files Table -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Files in Storage</h2>
            <div id="filesTable" class="hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Path</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody id="filesTableBody" class="bg-white divide-y divide-gray-200">
                    </tbody>
                </table>
            </div>
            <div id="noFiles" class="text-gray-500 text-center py-4">
                No files found. Click "List All Files" to load files.
            </div>
        </div>
    </div>

    <script>
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Test Connection
        document.getElementById('testConnection').addEventListener('click', async function() {
            const resultDiv = document.getElementById('connectionResult');
            resultDiv.classList.remove('hidden');
            resultDiv.innerHTML = '<div class="text-blue-500">Testing connection...</div>';

            try {
                const response = await fetch('/minio-test/test-connection');
                const data = await response.json();

                if (data.success) {
                    let connectionDetails = `
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            <p class="font-bold">✓ ${data.message}</p>
                            <p class="text-sm mt-2">Bucket: ${data.bucket}</p>
                            <p class="text-sm">Endpoint: ${data.endpoint}</p>
                            <p class="text-sm">Access Key: ${data.access_key}</p>
                    `;

                    if (data.can_list) {
                        connectionDetails += `<p class="text-sm">Can List: Yes</p>`;
                        connectionDetails += `<p class="text-sm">Total Files: ${data.total_files}</p>`;
                    } else {
                        connectionDetails += `<p class="text-sm text-yellow-600">Can List: No (Permission issue)</p>`;
                        connectionDetails += `<p class="text-xs text-red-600 mt-2">${data.list_error}</p>`;
                    }

                    connectionDetails += `</div>`;
                    resultDiv.innerHTML = connectionDetails;
                } else {
                    resultDiv.innerHTML = `
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <p class="font-bold">✗ ${data.message}</p>
                            <p class="text-sm mt-2">Error: ${data.error}</p>
                        </div>
                    `;
                }
            } catch (error) {
                resultDiv.innerHTML = `
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <p class="font-bold">✗ Connection Failed</p>
                        <p class="text-sm mt-2">Error: ${error.message}</p>
                    </div>
                `;
            }
        });

        // Upload File
        document.getElementById('uploadForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const resultDiv = document.getElementById('uploadResult');
            resultDiv.classList.remove('hidden');
            resultDiv.innerHTML = '<div class="text-blue-500">Uploading file...</div>';

            const formData = new FormData(this);

            try {
                const response = await fetch('/minio-test/upload', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();

                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            <p class="font-bold">✓ ${data.message}</p>
                            <p class="text-sm mt-2"><strong>Path:</strong> ${data.path}</p>
                            <p class="text-sm"><strong>Original Name:</strong> ${data.file_info.original_name}</p>
                            <p class="text-sm"><strong>Type:</strong> ${data.file_info.mime_type}</p>
                            <p class="text-sm"><strong>Size:</strong> ${formatBytes(data.file_info.size)}</p>
                            <hr class="my-2">
                            <p class="text-sm font-semibold mt-2">Presigned URL (Expires in 1 hour):</p>
                            <p class="text-xs"><a href="${data.presigned_url}" target="_blank" class="text-blue-600 underline break-all">${data.presigned_url}</a></p>
                            <p class="text-sm font-semibold mt-2">Proxy URL (Permanent, never expires):</p>
                            <p class="text-xs"><a href="${data.proxy_url}" target="_blank" class="text-green-600 underline break-all">${data.proxy_url}</a></p>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <p class="font-bold">✗ ${data.message}</p>
                            <p class="text-sm mt-2">Error: ${data.error}</p>
                        </div>
                    `;
                }
            } catch (error) {
                resultDiv.innerHTML = `
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <p class="font-bold">✗ Upload Failed</p>
                        <p class="text-sm mt-2">Error: ${error.message}</p>
                    </div>
                `;
            }
        });

        // List Files
        document.getElementById('listFiles').addEventListener('click', async function() {
            const resultDiv = document.getElementById('filesResult');
            resultDiv.classList.remove('hidden');
            resultDiv.innerHTML = '<div class="text-blue-500">Loading files...</div>';

            try {
                const response = await fetch('/minio-test/files');
                const data = await response.json();

                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            <p class="font-bold">✓ Found ${data.total} file(s)</p>
                        </div>
                    `;

                    // Update files table
                    const tableBody = document.getElementById('filesTableBody');
                    tableBody.innerHTML = '';

                    if (data.files.length > 0) {
                        document.getElementById('filesTable').classList.remove('hidden');
                        document.getElementById('noFiles').classList.add('hidden');

                        data.files.forEach(file => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${file.path}</td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="mb-1">
                                        <span class="text-xs font-semibold text-gray-600">Presigned:</span><br>
                                        <a href="${file.presigned_url}" target="_blank" class="text-blue-600 hover:text-blue-800 underline text-xs break-all">${file.presigned_url.substring(0, 50)}...</a>
                                    </div>
                                    <div>
                                        <span class="text-xs font-semibold text-green-600">Proxy:</span><br>
                                        <a href="${file.proxy_url}" target="_blank" class="text-green-600 hover:text-green-800 underline text-xs break-all">${file.proxy_url}</a>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formatBytes(file.size)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <button onclick="deleteFile('${file.path}')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                        Delete
                                    </button>
                                </td>
                            `;
                            tableBody.appendChild(row);
                        });
                    } else {
                        document.getElementById('filesTable').classList.add('hidden');
                        document.getElementById('noFiles').classList.remove('hidden');
                    }
                } else {
                    resultDiv.innerHTML = `
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <p class="font-bold">✗ ${data.message}</p>
                            <p class="text-sm mt-2">Error: ${data.error}</p>
                        </div>
                    `;
                }
            } catch (error) {
                resultDiv.innerHTML = `
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <p class="font-bold">✗ Failed to Load Files</p>
                        <p class="text-sm mt-2">Error: ${error.message}</p>
                    </div>
                `;
            }
        });

        // Delete File
        async function deleteFile(path) {
            if (!confirm(`Are you sure you want to delete ${path}?`)) {
                return;
            }

            try {
                const response = await fetch('/minio-test/delete', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ path: path })
                });

                const data = await response.json();

                if (data.success) {
                    alert('File deleted successfully');
                    // Refresh the files list
                    document.getElementById('listFiles').click();
                } else {
                    alert(`Failed to delete file: ${data.message}`);
                }
            } catch (error) {
                alert(`Error: ${error.message}`);
            }
        }

        // Helper function to format bytes
        function formatBytes(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }
    </script>
</body>
</html>
