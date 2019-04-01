import axios from 'axios'

export const getAsset = (id) => {
    return axios.get(`/api/v1/assets/${id}`)
        .then(response => response.data)
}

export const createAsset = (asset) => {
    return axios.post('/api/v1/assets', {asset:asset}, {headers :{'X-Requested-With': 'XMLHttpRequest'}}).then(response => response.data)
}

export const updateAsset = (asset) => {
    return axios.put(`/api/v1/assets/${asset.id}`, {asset:asset}, {headers :{'X-Requested-With': 'XMLHttpRequest'}}).then(response => response.data)
}

export const getCollections = () => {
    return axios.get('/api/v1/collections', {}, {headers :{'X-Requested-With': 'XMLHttpRequest'}}).then(response => response.data)
}

export const transactionId = () => {
    const randChar = () => {
        return Math.random().toString(16).slice(-4);
    }
    return randChar()+randChar()+'-'+randChar()+'-'+randChar()+randChar()+randChar() 
}