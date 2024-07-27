 <table id="outstanding" class="display table-auto w-full stripe row-border order-column">
     <thead>
         <tr>
             <th>No</th>
             <th>Kode Orders</th>
             <th>Nama Customer</th>
             <th>No So</th>
             <th>Po</th>
             <th>Tanggal</th>
             <th>Tanggal Kirim</th>
             <th>Nama Barang</th>
             <th>Volume</th>
             <th>Jumlah</th>
             @can('approve-order')
                 <th>Approve</th>
             @endcan
             @can('edit-order')
                 <th>Action</th>
             @elsecan('hapus-order')
                 <th>Action</th>
             @endcan
         </tr>
     </thead>
 </table>
