import axios from "axios";
export function isEmpty(str) {
    return !str || str.length === 0;
}

export async function uploadFile(formData) {
    try {
        let path = await axios.post("/api/file/upload-file", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        if (path.status == 200) {
            return { status: true, message: "/" + path.data };
        }
    } catch (err) {
        return { status: false, message: err };
    }
}
