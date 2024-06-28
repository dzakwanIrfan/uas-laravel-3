<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Orders') }}
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
        <x-bladewind::button color="green" onclick="createProduct()" class="mb-4">Add order</x-bladewind::button>
        <x-bladewind::table>
            <x-slot name="header">
                <th>Name</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Status</th>
                <th>Actions</th>
            </x-slot>
            @if ($orders->isEmpty())
                <tr>
                    <td colspan="6" class="text-center">Data is empty</td>
                </tr>
            @else  
                @foreach ($orders as $product)
                    <tr>
                        <td>{{ $product->customer->name }}</td>
                        <td>{{ $product->product->name }}</td>
                        <td>{{ $product->quantity }}</td>
                        <td>{{ $product->total }}</td>
                        <td>{{ $product->status }}</td>
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
        title="Create Product"
        ok_button_label="Create"
        cancel_button_label="Cancel"
        backdrop_can_close="false"
        ok_button_action="document.getElementById('create-product-form').submit();"
    >
        <div id="product-details-create">

        </div>
    </x-bladewind::modal>

    <x-bladewind::modal
        size="large"
        name="edit-product"
        title="Edit Product"
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
        title="View Product"
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
        title="Delete Product"
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
            fetch(`/orders/details`)
                .then(response => response.json())
                .then(data => {
                    fetch(`/orders/${id}/edit`)
                        .then(response => response.json())
                        .then(order => {
                            let productOptions = data.products.map(product => `<option value="${product.id}" ${product.id === order.product_id ? 'selected' : ''}>${product.name}</option>`).join('');
                            let customerOptions = data.customers.map(customer => `<option value="${customer.id}" ${customer.id === order.customer_id ? 'selected' : ''}>${customer.name}</option>`).join('');

                            let details = `
                                <form id="edit-product-form" action="{{ route('orders.update', '') }}/${id}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="id" value="${order.id}">
                                    <div class="mb-4">
                                        <label for="customer" class="block text-sm font-medium text-gray-700">Customer Name</label>
                                        <select id="customer" name="customer_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            ${customerOptions}
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label for="product" class="block text-sm font-medium text-gray-700">Product Name</label>
                                        <select id="product" name="product_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            ${productOptions}
                                        </select>
                                    </div>
                                    <div class="w-full">
                                        <label for="quantity" class="block text-sm font-medium text-gray-700">Product Quantity</label>
                                        <input type="number" name="quantity" id="quantity" value="${order.quantity}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                </form>
                            `;

                            document.getElementById('product-details-create').innerHTML = details;
                            showModal('create-product');
                        })
                        .catch(error => console.error('Error fetching order details:', error));
                })
                .catch(error => console.error('Error fetching details:', error));
        }

        function viewProduct(id) {
            fetch(`orders/${id}/details`)
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
                                    <label for="quantity" class="block text-sm font-medium text-gray-700">Product Quantity</label>
                                    <input type="number" name="quantity" id="quantity" value="${data.quantity}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" disabled>
                                </div>
                                <div class="w-full">
                                    <label for="price" class="block text-sm font-medium text-gray-700">Product Price</label>
                                    <input type="number" name="price" id="price" value="${data.price}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" disabled>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="description" class="block text-sm font-medium text-gray-700">Product Description</label>
                                <textarea name="description" id="description" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" disabled>${data.description}</textarea>
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
                <form id="delete-product-form" action="/orders/${id}" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" value="${id}" name="id">
                </form>
                <p>Are you sure you want to delete this product?</p> 
            `;
            document.getElementById('product-details-delete').innerHTML = details;
            showModal('delete-product');
        }

        function createProduct(id) {
            fetch(`/orders/details`)
                .then(response => response.json())
                .then(data => {
                    let productOptions = data.products.map(product => `<option value="${product.id}">${product.name}</option>`).join('');
                    let customerOptions = data.customers.map(customer => `<option value="${customer.id}">${customer.name}</option>`).join('');
                    let details = `
                        <form id="create-product-form" action="{{ route('orders.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="customer" class="block text-sm font-medium text-gray-700">Customer Name</label>
                                <select id="customer" name="customer_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option disabled selected>Select Customer</option>
                                    ${customerOptions}
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="product" class="block text-sm font-medium text-gray-700">Product Name</label>
                                <select id="product" name="product_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option disabled selected>Select Product</option>
                                    ${productOptions}
                                </select>
                            </div>
                            <div class="w-full">
                                <label for="quantity" class="block text-sm font-medium text-gray-700">Product Quantity</label>
                                <input type="number" name="quantity" id="quantity" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                        </form>
                    `;
                    document.getElementById('product-details-create').innerHTML = details;
                    showModal('create-product');
                })
                .catch(error => console.error('Error fetching details:', error));
        }
    </script>
</x-app-layout>