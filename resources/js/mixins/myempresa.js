
export default {
    // computed: {
    //     myempresa: {
    data: function () {

        return {

            myempresa: JSON.parse(localStorage.getItem("empresa")) != undefined && JSON.parse(localStorage.getItem("empresa"))['imagen'] != "" && JSON.parse(localStorage.getItem("empresa"))['imagen'] != null
                ? JSON.parse(localStorage.getItem("empresa"))['imagen']
                : "https://via.placeholder.com/150x50.png?text=" +
                'Compañia invitada'
                // JSON.parse(localStorage.getItem("empresa"))['razon_social'].replace(" ", "+")
        }

    },

    watch: {
        myempresa: function (val) {
            console.log(val);
            return val;
        }
    },

    // // getter
    // get: function () {
    //     return JSON.parse(localStorage.getItem("empresa"))['imagen'] != "" && JSON.parse(localStorage.getItem("empresa"))['imagen'] != null
    //         ? JSON.parse(localStorage.getItem("empresa"))['imagen']
    //         : "https://via.placeholder.com/150x50.png?text=" +
    //         JSON.parse(localStorage.getItem("empresa"))['razon_social'].replace(" ", "+");
    // },
    // // setter
    // set: function (newValue) {
    //     return newValue;
    // }

    //     }
    // }
}
