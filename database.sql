CREATE TABLE `afrn` (
  `id_afrn` int(11) NOT NULL,
  `tgl_afrn` date NOT NULL,
  `no_afrn` varchar(25) NOT NULL,
  `no_bpp` int(25) NOT NULL,
  `id_bridger` int(11) NOT NULL,
  `id_transportir` int(10) NOT NULL,
  `id_destinasi` int(10) NOT NULL,
  `id_tangki` int(10) NOT NULL,
  `dibuat` varchar(25) NOT NULL,
  `diperiksa` varchar(25) NOT NULL,
  `disetujui` varchar(25) NOT NULL,
  `rit` int(11) NOT NULL

  CREATE TABLE `bon` (
  `id_bon` int(10) NOT NULL,
  `no_afrn` varchar(20) NOT NULL,
  `tgl_rekam` date NOT NULL,
  `jlh_pengisian` float NOT NULL,
  `meter_awal` float NOT NULL,
  `total_meter_akhir` float NOT NULL,
  `masuk_dppu` time NOT NULL,
  `mulai_pengisian` time NOT NULL,
  `selesai_pengisian` time NOT NULL,
  `water_cont_ter` time NOT NULL,
  `keluar_dppu` time NOT NULL

  CREATE TABLE `bridger` (
  `id_bridger` int(10) NOT NULL,
  `id_trans` int(10) NOT NULL,
  `no_polisi` varchar(12) NOT NULL,
  `no_sertifikat` varchar(25) NOT NULL,
  `id_tipe_bridger` int(1) NOT NULL,
  `volume` int(11) NOT NULL,
  `tgl_serti_awal` varchar(25) NOT NULL,
  `tgl_serti_akhir` varchar(25) NOT NULL

  CREATE TABLE `destinasi` (
  `id_destinasi` int(10) NOT NULL,
  `nama_destinasi` varchar(50) NOT NULL,
  `alamat_destinasi` varchar(50) NOT NULL

CREATE TABLE `driver` (
  `id_driver` int(10) NOT NULL,
  `id_bridger` int(10) NOT NULL,
  `nama_driver` varchar(25) NOT NULL,
  `no_ktp` int(16) NOT NULL
  CREATE TABLE `tangki` (
  `id_tangki` int(10) NOT NULL,
  `no_tangki` int(10) NOT NULL,
  `no_bacth` varchar(20) NOT NULL,
  `source` varchar(20) NOT NULL,
  `doc_url` varchar(50) NOT NULL,
  `test_report_no` int(10) NOT NULL,
  `test_report_let` varchar(10) NOT NULL,
  `test_report_date` date NOT NULL,
  `density` float NOT NULL,
  `temperature` float NOT NULL,
  `cu` float NOT NULL,
  `water_contamination_ter` float NOT NULL
  ALTER TABLE `afrn`
  ADD PRIMARY KEY (`id_afrn`);
ALTER TABLE `bon`
  ADD PRIMARY KEY (`id_bon`);
ALTER TABLE `bridger`
  ADD PRIMARY KEY (`id_bridger`);
ALTER TABLE `destinasi`
  ADD PRIMARY KEY (`id_destinasi`);
ALTER TABLE `jarak_cair_t1`
  ADD PRIMARY KEY (`id_jarak_cair_t1`);
ALTER TABLE `jarak_t1`
  ADD PRIMARY KEY (`id_jarak_t1`);
ALTER TABLE `penomoran`
  ADD PRIMARY KEY (`id_penomoran`);
ALTER TABLE `role`
  ADD PRIMARY KEY (`id_role`);
ALTER TABLE `salib_ukur`
  ADD PRIMARY KEY (`id_ukur`);
ALTER TABLE `segel`
  ADD PRIMARY KEY (`id_segel`);
ALTER TABLE `tangki`
  ADD PRIMARY KEY (`id_tangki`);
ALTER TABLE `transportir`
  ADD PRIMARY KEY (`id_trans`);
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `id_role` (`id_role`);
ALTER TABLE `afrn`
  MODIFY `id_afrn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
ALTER TABLE `bon`
  MODIFY `id_bon` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `bridger`
  MODIFY `id_bridger` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `destinasi`
  MODIFY `id_destinasi` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `jarak_cair_t1`
  MODIFY `id_jarak_cair_t1` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
ALTER TABLE `jarak_t1`
  MODIFY `id_jarak_t1` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
ALTER TABLE `penomoran`
  MODIFY `id_penomoran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `role`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `salib_ukur`
  MODIFY `id_ukur` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `segel`
  MODIFY `id_segel` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `tangki`
  MODIFY `id_tangki` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `transportir`
  MODIFY `id_trans` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `user`
  MODIFY `id_user` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`);
COMMIT;