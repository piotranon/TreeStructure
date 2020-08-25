import axios from "axios";

export default axios.create({
    baseURL: process.env.MIX_API_URL,
    headers: {
        Authorization: "Bearer " + window.sessionStorage.getItem("user"),
    },
});
