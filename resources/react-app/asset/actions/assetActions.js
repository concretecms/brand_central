import {getAsset} from '../lib/assetServices'

export const fetchAsset = (id) =>{
    return (dispatch) => getAsset(id).then((asset) => dispatch(loadAsset(asset)))
}

export const loadAsset = (asset) => {
    return {
        type: "LOAD_ASSET",
        payload: asset
    }
}
export const setInput = (type, payload) => {
    return {
        type:type,
        payload:payload
    }
}

export const runAction = (type, payload) => {
    return {
        type: type,
        payload: payload
    }
}

export const setAssetId = (id) => {
    return{
        type: "SET_ASSET_ID",
        payload: id
    }
}

export const setAssetName = (name) => {
    return{
        type: "SET_ASSET_NAME",
        payload: name
    }
}

export const setAssetDesc = (desc) => {
    return {
        type: "SET_ASSET_DESC",
        payload: desc
    }
}

export const setAssetType = (type) => {
    return {
        type: "SET_ASSET_TYPE",
        payload: type
    }
}

export const setAssetLocation = (location) => {
    return {
        type: "SET_ASSET_LOCATION",
        payload: location
    }
}

export const setAssetTags = (tags) => {
    return {
        type: "SET_ASSET_TAGS",
        payload: tags
    }
}

export const setAssetThumb = (thumbnail) => {
    return {
        type: "SET_ASSET_THUMB",
        payload: thumbnail
    }
}

export const setAssetCollections = (collections) => {
    return {
        type: "SET_ASSET_COLLECTIONS",
        payload: collections
    }
}

export const setAssetFiles = (files) => {
    return {
        type: "SET_ASSET_FILES",
        payload: files
    }
}

/**
 * Factory for creating `TOGGLE_SAVING` payloads
 * This payload triggers redraw when the asset component begins or ends saving
 *
 * @param saving
 *
 * @returns {{type: string, payload: boolean}}
 */
export const toggleSaving = (saving) => {
    return {
        type: "TOGGLE_SAVING",
        payload: saving
    }
}
