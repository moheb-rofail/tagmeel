<x-layout>

    <div class="max-w-7xl mx-auto">
        <header class="mb-8 flex justify-between items-center">
            <h1 class="text-3xl font-extrabold text-gray-900">جرد مواد التجميل</h1>
            <button onclick="document.getElementById('createModal').classList.remove('hidden')" 
                    class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-150">
                + إضافة مادة جديدة
            </button>
        </header>
        
        <!-- Status Messages (Simulated Laravel Session Flash) -->
        @if(session('success'))
            {{-- Adjusted border to the right (border-r-4) for RTL --}}
            <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
                <p class="font-bold">نجاح!</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <div class="bg-white shadow-xl rounded-xl overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        {{-- Alignment changed from text-left to text-right --}}
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">اسم المنتج</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المخزون</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نقطة إعادة الطلب</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">سعر التكلفة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">سعر البيع</th>
                        {{-- Alignment changed from text-right to text-left for actions column --}}
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    {{-- Loop through items (Mock data for display) --}}
                    @forelse ($items as $item)
                        <tr class="{{ $item->stock_quantity <= $item->reorder_point ? 'low-stock' : '' }}">
                            {{-- Alignment changed from text-left to text-right --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">{{ $item->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $item->stock_quantity <= $item->reorder_point ? 'text-red-700' : 'text-gray-700' }} text-right">
                                {{ $item->stock_quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">{{ $item->reorder_point }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">${{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-pink-600 text-right">${{ number_format($item->selling_price, 2) }}</td>
                            {{-- Alignment changed from text-right to text-left --}}
                            <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                {{-- margin changed from mr-4 (margin-right) to ml-4 (margin-left) --}}
                                <button onclick="openEditModal({{ $item }})" class="text-indigo-600 hover:text-indigo-900 ml-4">تعديل</button>
                                <button onclick="confirmDelete('{{ $item->id }}')" class="text-red-600 hover:text-red-900">حذف</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 text-lg">لم يتم العثور على مواد. اضغط "إضافة مادة جديدة" للبدء.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination (Laravel's default) -->
        <div class="mt-4">
            {{-- {{ $items->links() }} --}}
            <p class="text-sm text-gray-500">عرض نتائج ترقيم الصفحات الوهمية.</p>
        </div>
    </div>

    <!-- Modals (Simple JS to handle forms/edit logic) -->

    {{-- 1. Create Modal --}}
    <div id="createModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white p-6 rounded-xl shadow-2xl w-full max-w-lg">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">إنشاء مادة جديدة</h2>
            <form action="{{ route('items.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    {{-- Name --}}
                    <div>
                        <label for="create_name" class="block text-sm font-medium text-gray-700">الاسم</label>
                        <input type="text" name="name" id="create_name" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 p-2 text-right">
                    </div>
                    {{-- Description --}}
                    <div>
                        <label for="create_description" class="block text-sm font-medium text-gray-700">الوصف</label>
                        <textarea name="description" id="create_description" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 p-2 text-right"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Cost Price --}}
                        <div>
                            <label for="create_unit_price" class="block text-sm font-medium text-gray-700">سعر التكلفة ($)</label>
                            <input type="number" name="unit_price" id="create_unit_price" step="0.01" min="0" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 p-2 text-right">
                        </div>
                        {{-- Selling Price --}}
                        <div>
                            <label for="create_selling_price" class="block text-sm font-medium text-gray-700">سعر البيع ($)</label>
                            <input type="number" name="selling_price" id="create_selling_price" step="0.01" min="0" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 p-2 text-right">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Initial Stock Quantity --}}
                        <div>
                            <label for="create_stock_quantity" class="block text-sm font-medium text-gray-700">المخزون الأولي</label>
                            <input type="number" name="stock_quantity" id="create_stock_quantity" min="0" required value="0" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 p-2 text-right">
                        </div>
                        {{-- Reorder Point --}}
                        <div>
                            <label for="create_reorder_point" class="block text-sm font-medium text-gray-700">نقطة إعادة الطلب</label>
                            <input type="number" name="reorder_point" id="create_reorder_point" min="0" required value="5" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 p-2 text-right">
                        </div>
                    </div>

                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-150">إلغاء</button>
                    <button type="submit" class="px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition duration-150">حفظ المادة</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 2. Edit Modal --}}
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white p-6 rounded-xl shadow-2xl w-full max-w-lg">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">تعديل تفاصيل المادة</h2>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    {{-- Name --}}
                    <div>
                        <label for="edit_name" class="block text-sm font-medium text-gray-700">الاسم</label>
                        <input type="text" name="name" id="edit_name" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 p-2 text-right">
                    </div>
                    {{-- Description --}}
                    <div>
                        <label for="edit_description" class="block text-sm font-medium text-gray-700">الوصف</label>
                        <textarea name="description" id="edit_description" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 p-2 text-right"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Cost Price --}}
                        <div>
                            <label for="edit_unit_price" class="block text-sm font-medium text-gray-700">سعر التكلفة ($)</label>
                            <input type="number" name="unit_price" id="edit_unit_price" step="0.01" min="0" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 p-2 text-right">
                        </div>
                        {{-- Selling Price --}}
                        <div>
                            <label for="edit_selling_price" class="block text-sm font-medium text-gray-700">سعر البيع ($)</label>
                            <input type="number" name="selling_price" id="edit_selling_price" step="0.01" min="0" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 p-2 text-right">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Current Stock (Read-Only/Editable for Correction) --}}
                        <div>
                            <label for="edit_stock_quantity" class="block text-sm font-medium text-gray-700">المخزون الحالي</label>
                            {{-- IMPORTANT: Stock should ideally only be changed via Sale/Purchase, but we allow editing here for corrections/initial setup --}}
                            <input type="number" name="stock_quantity" id="edit_stock_quantity" min="0" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 p-2 text-right">
                        </div>
                        {{-- Reorder Point --}}
                        <div>
                            <label for="edit_reorder_point" class="block text-sm font-medium text-gray-700">نقطة إعادة الطلب</label>
                            <input type="number" name="reorder_point" id="edit_reorder_point" min="0" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 p-2 text-right">
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-150">إلغاء</button>
                    <button type="submit" class="px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition duration-150">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(item) {
            const form = document.getElementById('editForm');
            
            // Set the form action to the correct update route (mocking Laravel route helper)
            form.action = `/items/${item.id}`; 

            document.getElementById('edit_name').value = item.name;
            document.getElementById('edit_description').value = item.description || '';
            document.getElementById('edit_unit_price').value = item.unit_price;
            document.getElementById('edit_selling_price').value = item.selling_price;
            document.getElementById('edit_stock_quantity').value = item.stock_quantity;
            document.getElementById('edit_reorder_point').value = item.reorder_point;

            document.getElementById('editModal').classList.remove('hidden');
        }

        function confirmDelete(itemId) {
            // Updated confirmation text to Arabic
            if (window.confirm('هل أنت متأكد من حذف هذه المادة؟ لا يمكن التراجع عن هذا الإجراء.')) {
                // Mocking a form submission for DELETE method
                const form = document.createElement('form');
                form.action = `/items/${itemId}`;
                form.method = 'POST';
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                document.body.appendChild(form);
                // form.submit(); // Uncomment to submit the delete request
                
                // Alerting instead of actual submission in this limited environment
                console.log(`تم إرسال طلب الحذف إلى /items/${itemId}.`);
                alert('تم محاكاة طلب الحذف في وحدة التحكم.'); 
            }
        }
    </script>
</x-layout>