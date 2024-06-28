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
        <x-bladewind::table>
            <x-slot name="header">
                <th>Order Id</th>
                <th>Total</th>
                <th>Method</th>
                <th>Status</th>
                <th>Actions</th>
            </x-slot>
            @if ($payments->isEmpty())
                <tr>
                    <td colspan="6" class="text-center">Data is empty</td>
                </tr>
            @else  
                @foreach ($payments as $payment)
                    <tr>
                        <td>ID No. {{ $payment->id }}</td>
                        <td>{{ $payment->order->total }}</td>
                        <td>{{ $payment->method }}</td>
                        <td>{{ $payment->order->status }}</td>
                        <td>
                            <x-bladewind::button size="tiny" color="yellow" onClick="editProduct({{ $payment->id }})">Edit</x-bladewind::button>
                        </td>
                    </tr>
                @endforeach
            @endif
        </x-bladewind::table>
    </div>

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

    <script>
        function editProduct(id) {
            fetch(`/payments/${id}/details`)
                .then(response => response.json())
                .then(data => {
                    let details = `
                        <form id="edit-product-form" action="{{ route('payments.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" value="${id}">
                            <div class="mb-4">
                                <label for="method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                                <select id="method" name="method" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option disabled selected>Select Payment Method</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Credit">Credit</option>
                                    <option value="Debit">Debit</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="status" class="block text-sm font-medium text-gray-700">Payment Status</label>
                                <select id="status" name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option disabled selected>Select Payment Status</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Paid">Paid</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
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
    </script>
</x-app-layout>