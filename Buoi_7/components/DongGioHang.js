const DongGioHang = {
    props: {
        id: {
            type: Number,
            required: true
        },
        ten: {
            type: String,
            required: true
        },
        donGia: {
            type: Number,
            required: true
        },
        soLuong: {
            type: Number,
            required: true
        }
    },
    emits: ["doi-so-luong", "yeu-cau-xoa"],
    computed: {
        thanhTien() {
            return this.donGia * this.soLuong;
        }
    },
    methods: {
        dinhDangTien(soTien) {
            return soTien.toLocaleString("vi-VN") + " đ";
        },
        doiSoLuong(buoc) {
            // Không sửa prop soLuong, chỉ báo số lượng mới để component cha tự cập nhật mảng.
            this.$emit("doi-so-luong", { id: this.id, soLuong: this.soLuong + buoc });
        },
        yeuCauXoa() {
            this.$emit("yeu-cau-xoa", this.id);
        }
    },
    template: `
        <tr>
            <td>{{ ten }}</td>
            <td class="phai">{{ dinhDangTien(donGia) }}</td>
            <td class="phai">
                <button class="phu" @click="doiSoLuong(-1)" :disabled="soLuong <= 1">-</button>
                <span class="so-luong">{{ soLuong }}</span>
                <button class="phu" @click="doiSoLuong(1)" :disabled="soLuong >= 99">+</button>
            </td>
            <td class="phai">{{ dinhDangTien(thanhTien) }}</td>
            <td>
                <button class="xoa" @click="yeuCauXoa">Xóa</button>
            </td>
        </tr>
    `
};
