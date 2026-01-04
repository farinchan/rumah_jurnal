      <div class="card mb-12">
          <div class="card-body flex-column p-5">
              <div class="d-flex align-items-center h-lg-300px p-5 p-lg-15">
                  <div class="d-flex flex-column align-items-start justift-content-center flex-equal me-5">
                      <h1 class="fw-bold fs-4 fs-lg-1 text-gray-800 mb-5 mb-lg-10">Kirim Pesan</h1>
                     <p class="fw-semibold fs-6 fs-lg-5 text-gray-600 mb-5 mb-lg-10">
                          Kirim pesan Whatsapp dengan mudah dan cepat. Pilih pengguna, masukkan pesan, dan kirim dengan satu klik tombol. Anda juga dapat mengirim gambar sebagai bagian dari pesan Anda.
                      </p>

                  </div>
                  <div class="flex-equal d-flex justify-content-center align-items-end ms-5">
                      <img src="{{ asset("back/media/illustrations/sketchy-1/15.png") }}" alt=""
                          class="mw-100 mh-125px mh-lg-275px mb-lg-n12" />
                  </div>
              </div>
              <div class="card-rounded bg-light d-flex flex-stack flex-wrap p-5">
                  <ul class="nav flex-wrap border-transparent fw-bold">
                      <li class="nav-item my-1">
                          <a class="btn btn-color-gray-600 btn-active-secondary btn-active-color-primary fw-bolder fs-8 fs-lg-base nav-link px-3 px-lg-8 mx-1 text-uppercase
                              {{ request()->routeIs('back.chatery-whatsapp.message.sendMessage') ? 'active' : '' }}"
                              href="{{ route("back.chatery-whatsapp.message.sendMessage") }}">Kirim Pesan</a>
                      </li>
                      <li class="nav-item my-1">
                          <a class="btn btn-color-gray-600 btn-active-secondary btn-active-color-primary fw-bolder fs-8 fs-lg-base nav-link px-3 px-lg-8 mx-1 text-uppercase
                              {{ request()->routeIs('back.chatery-whatsapp.message.sendImage') ? 'active' : '' }}"
                              href="{{ route("back.chatery-whatsapp.message.sendImage") }}">Kirim Pesan Dengan Gambar</a>
                      </li>
                      <li class="nav-item my-1">
                          <a class="btn btn-color-gray-600 btn-active-secondary btn-active-color-primary fw-bolder fs-8 fs-lg-base nav-link px-3 px-lg-8 mx-1 text-uppercase
                              {{ request()->routeIs('back.chatery-whatsapp.message.sendBulkMessage') ? 'active' : '' }}"
                              href="{{ route("back.chatery-whatsapp.message.sendBulkMessage") }}">Kirim Pesan Massal</a>
                      </li>
                  </ul>
              </div>
          </div>
      </div>
