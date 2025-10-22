<x-layout>

    <div class="max-w-7xl mx-auto">
        <header class="mb-8 flex justify-between items-center">
            <h1 class="text-3xl font-extrabold text-gray-900">سجل الموردين</h1>
            <button onclick="document.getElementById('createModal').classList.remove('hidden')" 
                    class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-150">
                + إضافة مورد جديد
            </button>
        </header>
        
        <!-- Status Messages -->
        @if(session('success'))
            <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
                <p class="font-bold">نجاح!</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg" role="alert">
                <p class="font-bold">خطأ!</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="bg-white shadow-xl rounded-xl overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">اسم المورد</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">شخص الاتصال</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم الهاتف</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">البريد الإلكتروني</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    {{-- Loop through suppliers (Mock data for display) --}}
                    @forelse ($suppliers as $supplier)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">{{ $supplier->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-right">{{ $supplier->contact_person ?? 'غير محدد' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-right">{{ $supplier->phone ?? 'لا يوجد' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-right">{{ $supplier->email ?? 'لا يوجد' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                <button onclick="openEditModal({{ $supplier }})" class="text-indigo-600 hover:text-indigo-900 ml-4">تعديل</button>
                                <button onclick="confirmDeleteSupplier('{{ $supplier->id }}')" class="text-red-600 hover:text-red-900">حذف</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-lg">لم يتم العثور على موردين. اضغط "إضافة مورد جديد" للبدء.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination (Laravel's default) -->
        <div class="mt-4">
            {{-- {{ $suppliers->links() }} --}}
            <p class="text-sm text-gray-500">عرض نتائج ترقيم الصفحات الوهمية.</p>
        </div>
    </div>

    <!-- Modals -->

    {{-- 1. Create Supplier Modal --}}
    <div id="createModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white p-6 rounded-xl shadow-2xl w-full max-w-lg">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">إضافة مورد جديد</h2>
            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    {{-- Name --}}
                    <div>
                        <label for="create_name" class="block text-sm font-medium text-gray-700">اسم المورد (إلزامي)</label>
                        <input type="text" name="name" id="create_name" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 p-2 text-right">
                    </div>
                    {{-- Contact Person --}}
                    <div>
                        <label for="create_contact_person" class="block text-sm font-medium text-gray-700">شخص الاتصال</label>
                        <input type="text" name="contact_person" id="create_contact_person" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 p-2 text-right">
                    </div>
                    {{-- Phone --}}
                    <div>
                        <label for="create_phone" class="block text-sm font-medium text-gray-700">رقم الهاتف</label>
                        <input type="text" name="phone" id="create_phone" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 p-2 text-right">
                    </div>
                    {{-- Email --}}
                    <div>
                        <label for="create_email" class="block text-sm font-medium text-gray-700">البريد الإلكتروني</label>
                        <input type="email" name="email" id="create_email" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 p-2 text-right">
                    </div>
                    {{-- Address --}}
                    <div>
                        <label for="create_address" class="block text-sm font-medium text-gray-700">العنوان</label>
                        <textarea name="address" id="create_address" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 p-2 text-right"></textarea>
                    </div>

                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-150">إلغاء</button>
                    <button type="submit" class="px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition duration-150">حفظ المورد</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 2. Edit Supplier Modal --}}
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white p-6 rounded-xl shadow-2xl w-full max-w-lg">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">تعديل بيانات المورد</h2>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    {{-- Name --}}
                    <div>
                        <label for="edit_name" class="block text-sm font-medium text-gray-700">اسم المورد (إلزامي)</label>
                        <input type="text" name="name" id="edit_name" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 p-2 text-right">
                    </div>
                    {{-- Contact Person --}}
                    <div>
                        <label for="edit_contact_person" class="block text-sm font-medium text-gray-700">شخص الاتصال</label>
                        <input type="text" name="contact_person" id="edit_contact_person" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 p-2 text-right">
                    </div>
                    {{-- Phone --}}
                    <div>
                        <label for="edit_phone" class="block text-sm font-medium text-gray-700">رقم الهاتف</label>
                        <input type="text" name="phone" id="edit_phone" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 p-2 text-right">
                    </div>
                    {{-- Email --}}
                    <div>
                        <label for="edit_email" class="block text-sm font-medium text-gray-700">البريد الإلكتروني</label>
                        <input type="email" name="email" id="edit_email" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 p-2 text-right">
                    </div>
                    {{-- Address --}}
                    <div>
                        <label for="edit_address" class="block text-sm font-medium text-gray-700">العنوان</label>
                        <textarea name="address" id="edit_address" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 p-2 text-right"></textarea>
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
        // Mocking the route helper for forms (replace with actual Laravel routes)
        function getSupplierRoute(id) {
            return `/suppliers/${id}`;
        }
        
        function openEditModal(supplier) {
            const form = document.getElementById('editForm');
            
            // Set the form action to the correct update route 
            form.action = getSupplierRoute(supplier.id); 

            // Populate fields
            document.getElementById('edit_name').value = supplier.name;
            document.getElementById('edit_contact_person').value = supplier.contact_person || '';
            document.getElementById('edit_phone').value = supplier.phone || '';
            document.getElementById('edit_email').value = supplier.email || '';
            document.getElementById('edit_address').value = supplier.address || '';

            document.getElementById('editModal').classList.remove('hidden');
        }

        function confirmDeleteSupplier(supplierId) {
            if (window.confirm('هل أنت متأكد من حذف هذا المورد؟ لا يمكن حذفه إذا كانت لديه عمليات شراء مسجلة.')) {
                // Mocking a form submission for DELETE method
                const form = document.createElement('form');
                form.action = getSupplierRoute(supplierId);
                form.method = 'POST';
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                document.body.appendChild(form);
                // form.submit(); // Uncomment to submit the delete request
                
                console.log(`تم إرسال طلب الحذف إلى /suppliers/${supplierId}.`);
                console.log('تم محاكاة طلب الحذف في وحدة التحكم.'); 
            }
        }
    </script>
</x-layout>