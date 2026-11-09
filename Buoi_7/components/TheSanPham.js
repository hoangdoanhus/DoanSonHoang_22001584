const TheSanPham = {
    props: {
        ten: {
            type: String,
            required: true
        },
        gia: {
            type: Number,
            required: true
        },
        anh: {
            type: String,
            default: "anh/mac-dinh.svg"
        },
        moTa: {
            type: String,
            default: "Sản phẩm này chưa có mô tả."
        },
        ton: {
            type: Number,
            default: 0
        }
    },
    data() {
        // Mỗi thẻ giữ trạng thái mở mô tả của riêng nó nên các thẻ khác không bị ảnh hưởng.
        return {
            hienMoTa: false
        };
    },
    computed: {
        conHang() {
            return this.ton > 0;
        },
        giaHienThi() {
            return this.gia.toLocaleString("vi-VN") + " đ";
        }
    },
    template: `
        <div class="the-sp">
            <img :src="anh" :alt="'Ảnh sản phẩm ' + ten">

            <div class="than">
                <h3>{{ ten }}</h3>
                <p class="gia">{{ giaHienThi }}</p>

                <p>
                    <span class="nhan" :class="conHang ? 'nhan-con-hang' : 'nhan-het-hang'">
                        {{ conHang ? 'Còn hàng: ' + ton : 'Hết hàng' }}
                    </span>
                </p>

                <p class="mo-ta" v-show="hienMoTa">{{ moTa }}</p>

                <div class="nhom-nut">
                    <button :disabled="!conHang">Mua</button>
                    <button class="phu" @click="hienMoTa = !hienMoTa">
                        {{ hienMoTa ? 'Ẩn mô tả' : 'Xem mô tả' }}
                    </button>
                </div>
            </div>
        </div>
    `
};
