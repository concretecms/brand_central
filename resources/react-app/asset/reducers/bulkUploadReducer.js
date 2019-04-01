const InitialState = {
    assets:[],
    maxAssets:30,
    currentCollection:null,
    currentAsset:null,
    currentAssetFid:null,
    isLoadingTags:false,
    isProcessingSave:false,
    errorCollection:false,
    errorFiles:false,
    errorFilesMsg:'',
    errorAsset:null,
    errorGlobal:false,
    errorGlobalMsg:'',
    collections:[]
}

const bulkUploadReducer = (
    state = InitialState, action) => {
    switch (action.type){
        case "SET_CURRENT_COLLECTION_BULK":
            return Object.assign({}, state, {
                currentCollection: action.payload
            })
        case "SET_COLLECTION_ERROR":
            return Object.assign({}, state, {
                errorCollection: action.payload
            })
        case "SET_FILES_ERROR":
            return Object.assign({}, state, {
                errorFiles: action.payload
            })
        case "SET_FILES_ERROR_MSG":
            return Object.assign({}, state, {
                errorFilesMsg: action.payload
            })
        case "SET_GLOBAL_ERROR":
            return Object.assign({}, state, {
                errorGlobal: action.payload
            })
        case "SET_GLOBAL_ERROR_MSG":
            return Object.assign({}, state, {
                errorGlobalMsg: action.payload
            })
        case "SET_ASSET_ERROR":
            return Object.assign({}, state, {
                errorAsset: action.payload
            })
        case "SET_IS_PROCESSING_SAVE":
            return Object.assign({}, state, {
                isProcessingSave:action.payload
            })
        case "SET_CURRENT_ASSET_BULK":
            return Object.assign({}, state, {
                currentAsset: action.payload
            })
        case "SET_CURRENT_ASSET_FID_BULK":
            return Object.assign({}, state, {
                currentAssetFid: action.payload
            })
        case "SET_IS_LOADING_TAGS":
            return Object.assign({}, state, {
                isLoadingTags:action.payload
            })
        case "SET_ASSET_BULK":
            return Object.assign({}, state, {
                assets: state.assets.concat({
                    id: action.payload.id,
                    desc: action.payload.desc,
                    name: action.payload.name,
                    type:action.payload.type,
                    img: action.payload.img,
                    fid: action.payload.fid,
                    tags: action.payload.tags,
                    isLoading: action.payload.isLoading,
                    isLoadingTags:action.payload.isLoadingTags,
                    isNameEditMode: action.payload.isNameEditMode,
                    isDescEditMode: action.payload.isDescEditMode,
                    hasErrors:action.payload.hasErrors,
                    errorMsg:action.payload.errorMsg
                })
            })
        case "UPDATE_ASSET_BULK_IS_LOADING":
            return Object.assign({}, state, {
                assets: state.assets.map(asset => {
                    if(asset.id !== action.payload.id){
                        return asset
                    }
                    return Object.assign({}, asset, {
                        isLoading: action.payload.isLoading,
                    })
                })
            })
        case "UPDATE_ASSET_BULK_IS_LOADING_TAGS":
            return Object.assign({}, state, {
                assets: state.assets.map(asset => {
                    if(asset.id !== action.payload.id){
                        return asset
                    }
                    return Object.assign({}, asset, {
                        isLoadingTags: action.payload.isLoadingTags,
                    })
                })
            })
        case "UPDATE_ASSET_BULK_IMG":
            return Object.assign({}, state, {
                assets: state.assets.map(asset => {
                    if(asset.id !== action.payload.id){
                        return asset
                    }
                    return Object.assign({}, asset, {
                        fid: action.payload.fid,
                        img: action.payload.img,
                        name: action.payload.name,
                        type:action.payload.type,
                        hasErrors:action.payload.hasErrors,
                        errorMsg:action.payload.errorMsg
                    })
                })
            })
        case "UPDATE_ASSET_BULK_TYPE":
            return Object.assign({}, state, {
                assets: state.assets.map(asset => {
                    if(asset.id !== action.payload.id){
                        return asset
                    }
                    return Object.assign({}, asset, {
                        type: action.payload.type,
                    })
                })
            })
        case "UPDATE_ASSET_BULK_NAME":
            return Object.assign({}, state, {
                assets: state.assets.map(asset => {
                    if(asset.id !== action.payload.id){
                        return asset
                    }
                    return Object.assign({}, asset, {
                        name: action.payload.name,
                    })
                })
            })
        case "UPDATE_ASSET_BULK_DESC":
            return Object.assign({}, state, {
                assets: state.assets.map(asset => {
                    if(asset.id !== action.payload.id){
                        return asset
                    }
                    return Object.assign({}, asset, {
                        desc: action.payload.desc,
                    })
                })
            })
        case "SET_ASSET_BULK_NAME_EDIT_MODE":
            return Object.assign({}, state, {
                assets: state.assets.map(asset => {
                    if(asset.id !== action.payload.id){
                        return asset
                    }
                    return Object.assign({}, asset, {
                        isNameEditMode: action.payload.isNameEditMode,
                    })
                })
            })
        case "SET_ASSET_BULK_DESC_EDIT_MODE":
            return Object.assign({}, state, {
                assets: state.assets.map(asset => {
                    if(asset.id !== action.payload.id){
                        return asset
                    }
                    return Object.assign({}, asset, {
                        isDescEditMode: action.payload.isDescEditMode,
                    })
                })
            })
        case "SET_ASSET_BULK_TAG":
            return Object.assign({}, state, {
                assets: state.assets.map(asset => {
                    if(asset.id !== action.payload.id){
                        return asset
                    }

                    if (!action.payload.name || !action.payload.name.trim()) {
                        return asset;
                    }

                    let assetTagMatch = asset.tags.filter(function (tag, key) {
                        return tag.name.toLowerCase().trim() === action.payload.name.toLowerCase().trim();
                    }).length;

                    if (assetTagMatch) {
                        return asset;
                    }

                    return Object.assign({}, asset, {
                        tags: asset.tags.concat({
                            id:action.payload.tid,
                            name:action.payload.name
                        })
                    })
                })
            })
        case "REMOVE_ASSET_BULK_TAG":
            return Object.assign({}, state, {
                assets: state.assets.map(asset => {
                    if(asset.id !== action.payload.id){
                        return asset
                    }
                    return Object.assign({}, asset, {
                        tags: asset.tags.filter(tag => tag.id !== action.payload.tid)
                    })
                })
            })
        case "REMOVE_ASSET_BULK":
            return Object.assign({}, state, {
                assets: state.assets.filter(asset => asset.id !== action.payload)
            })
        case "SET_ASSET_BULK_COLLECTIONS":
            if (!action.payload.name || !action.payload.name.trim()) {
                return state;
            }

            let collectionMatch = state.collections.filter(function (tag, key) {
                return tag.name.toLowerCase().trim() === action.payload.name.toLowerCase().trim();
            }).length;

            if (collectionMatch) {
                return state;
            }
            return Object.assign({}, state, {
                collections: state.collections.concat({
                    id:action.payload.id,
                    name:action.payload.name
              })
            })
        case "REMOVE_ASSET_BULK_COLLECTION":
            return Object.assign({}, state, {
                collections: state.collections.filter(item => item.id !== action.payload)
            })
        default: return state
    }
}

export default bulkUploadReducer
