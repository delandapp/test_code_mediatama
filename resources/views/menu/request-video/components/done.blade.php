 <table id="tabel_done" class="display table-auto w-full stripe row-border order-column">
     <thead>
         <tr>
             <th>No</th>
             <th>Kode Request</th>
             <th>Nama Materi</th>
             <th>Nama Customer</th>
             <th>Tanngal</th>
             <th>Status</th>
             <th>Expired</th>
             <th>Lama Menonton</th>
             <th>Tanggal Approve</th>
             @can('edit-video')
                 <th>Action</th>
             @elsecan('hapus-video')
                 <th>Action</th>
             @endcan
         </tr>
     </thead>
 </table>
