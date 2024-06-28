<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Products') }}
        </h2>
    </x-slot>
    @if (session('success'))
        <x-bladewind::alert
            type="success">
            {{ session('success') }}
        </x-bladewind::alert>
    @endif
    @if (session('error'))
        <x-bladewind::alert
            type="error">
            {{ session('error') }}
        </x-bladewind::alert>
    @endif
    <div class="px-40 mx-auto mt-8">
        <x-bladewind::button color="green" onclick="showModal('create-product')" class="mb-4">Add customer</x-bladewind::button>
        <x-bladewind::table>
            <x-slot name="header">
                <th>Name</th>
                <th>Email</th>
                <th>Address</th>
                <th>Actions</th>
            </x-slot>
            @if ($customers->isEmpty())
                <tr>
                    <td colspan="4" class="text-center">Data is empty</td>
                </tr>
            @else  
                @foreach ($customers as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->email }}</td>
                        <td>{{ $product->address }}</td>
                        <td>
                            <x-bladewind::button size="tiny" onClick="viewProduct({{ $product->id }})">View</x-bladewind::button>
                            <x-bladewind::button size="tiny" color="yellow" onClick="editProduct({{ $product->id }})">Edit</x-bladewind::button>
                            <x-bladewind::button size="tiny" color="red" onclick="deleteProduct({{ $product->id }})">Delete</x-bladewind::button>
                        </td>
                    </tr>
                @endforeach
            @endif
        </x-bladewind::table>
    </div>

    
    <x-bladewind::modal
        size="large"
        name="create-product"
        title="Create Customer"
        ok_button_label="Create"
        cancel_button_label="Cancel"
        backdrop_can_close="false"
        ok_button_action="document.getElementById('create-product-form').submit();"
    >
        <form id="create-product-form" action="{{ route('customers.store') }}" method="POST">
            @csrf
            <x-bladewind::input  
                name="name"
                id="name"
                label="Customer Name"
                required="true"
            />
            <div class="flex w-full gap-4">
                <div class="w-full">
                    <x-bladewind::input  
                        name="email"
                        id="email"
                        label="Customer Email"
                        required="true"
                    />
                </div>
                <div class="w-full">
                    <x-bladewind::input  
                        name="address"
                        id="address"
                        label="Customer Address"
                        required="true"
                    />
                </div>
            </div>
        </form>
    </x-bladewind::modal>

    <x-bladewind::modal
        size="large"
        name="edit-product"
        title="Edit Customer"
        ok_button_label="Update"
        cancel_button_label="Cancel"
        backdrop_can_close="false"
        ok_button_action="document.getElementById('edit-product-form').submit();"
    >
    <div id="product-details-edit">
        <!-- Product details will be loaded here -->
    </div>   
    </x-bladewind::modal>

    <x-bladewind::modal
        size="large"
        name="view-product"
        title="View Customer"
        ok_button_label=""
        cancel_button_label="Back"
    >
    <div id="product-details-view">
        <!-- Product details will be loaded here -->
    </div>   
    </x-bladewind::modal>

    <x-bladewind::modal
        size="large"
        name="delete-product"
        title="Delete Customer"
        ok_button_label="Delete"
        cancel_button_label="Back"
        ok_button_action="document.getElementById('delete-product-form').submit();"
    >
        <div id="product-details-delete">
            <!-- Product details will be loaded here -->
        </div>    
    </x-bladewind::modal>

    <script>
        function editProduct(id) {
            console.log('Edit product clicked for id:', id);
            fetch(`customers/${id}/details`)
                .then(response => response.json())
                .then(data => {
                    let details = `
                        <form id="edit-product-form" action="/customers/${id}/update" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" value="${data.id}">
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Customer Name</label>
                                <input type="text" name="name" id="name" value="${data.name}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div class="flex w-full gap-4 mb-4">
                                <div class="w-full">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Customer Email</label>
                                    <input type="email" name="email" id="email" value="${data.email}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <div class="w-full">
                                    <label for="address" class="block text-sm font-medium text-gray-700">Customer Address</label>
                                    <input type="text" name="address" id="address" value="${data.address}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                            </div>
                        </form>
                    `;
                    document.getElementById('product-details-edit').innerHTML = details;
                    showModal('edit-product');
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function viewProduct(id) {
            fetch(`customers/${id}/details`)
                .then(response => response.json())
                .then(data => {
                    let details = `
                            <input type="hidden" name="id" value="${data.id}">
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Product Name</label>
                                <input type="text" name="name" id="name" value="${data.name}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" disabled>
                            </div>
                            <div class="flex w-full gap-4 mb-4">
                                <div class="w-full">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Product Email</label>
                                    <input type="email" name="email" id="email" value="${data.email}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" disabled>
                                </div>
                                <div class="w-full">
                                    <label for="address" class="block text-sm font-medium text-gray-700">Product Address</label>
                                    <input type="text" name="address" id="address" value="${data.address}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" disabled>
                                </div>
                            </div>
                    `;
                    document.getElementById('product-details-view').innerHTML = details;
                    showModal('view-product');
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function deleteProduct(id) {
            let details = `
                <form id="delete-product-form" action="/customers/${id}" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" value="${id}" name="id">
                </form>
                <p>Are you sure you want to delete this customer?</p> 
            `;
            document.getElementById('product-details-delete').innerHTML = details;
            showModal('delete-product');
        }
    </script>
</x-app-layout>