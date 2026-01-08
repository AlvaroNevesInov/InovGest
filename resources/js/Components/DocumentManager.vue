<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Textarea } from '@/Components/ui/textarea';

const props = defineProps({
    documents: {
        type: Array,
        default: () => []
    },
    documentableType: {
        type: String,
        required: true
    },
    documentableId: {
        type: Number,
        required: true
    }
});

const selectedFile = ref(null);
const description = ref('');
const isUploading = ref(false);

const selectFile = (event) => {
    selectedFile.value = event.target.files[0];
};

const uploadDocument = () => {
    if (!selectedFile.value) {
        alert('Por favor, selecione um ficheiro');
        return;
    }

    isUploading.value = true;

    const formData = new FormData();
    formData.append('file', selectedFile.value);
    formData.append('documentable_type', props.documentableType);
    formData.append('documentable_id', props.documentableId);
    formData.append('description', description.value);

    router.post(route('documents.store'), formData, {
        preserveScroll: true,
        onSuccess: () => {
            selectedFile.value = null;
            description.value = '';
            isUploading.value = false;

            // Reset file input
            const fileInput = document.getElementById('document-file');
            if (fileInput) fileInput.value = '';
        },
        onError: () => {
            isUploading.value = false;
        }
    });
};

const deleteDocument = (documentId) => {
    if (!confirm('Tem a certeza que deseja eliminar este documento?')) {
        return;
    }

    router.delete(route('documents.destroy', documentId), {
        preserveScroll: true
    });
};

const downloadDocument = (downloadUrl) => {
    window.location.href = downloadUrl;
};

const getFileIcon = (mimeType) => {
    if (mimeType.startsWith('image/')) return '🖼️';
    if (mimeType === 'application/pdf') return '📄';
    if (mimeType.includes('word') || mimeType.includes('document')) return '📝';
    if (mimeType.includes('sheet') || mimeType.includes('excel')) return '📊';
    if (mimeType.includes('zip') || mimeType.includes('rar')) return '📦';
    return '📎';
};
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Documentos</CardTitle>
        </CardHeader>
        <CardContent>
            <!-- Upload Form -->
            <div class="mb-6 space-y-4 rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                <h3 class="text-sm font-medium">Carregar Novo Documento</h3>

                <div>
                    <Label for="document-file">Ficheiro</Label>
                    <Input
                        id="document-file"
                        type="file"
                        @change="selectFile"
                        :disabled="isUploading"
                        class="mt-1"
                    />
                    <p v-if="selectedFile" class="mt-1 text-sm text-gray-500">
                        Selecionado: {{ selectedFile.name }} ({{ (selectedFile.size / 1024).toFixed(2) }} KB)
                    </p>
                </div>

                <div>
                    <Label for="document-description">Descrição (opcional)</Label>
                    <Textarea
                        id="document-description"
                        v-model="description"
                        :disabled="isUploading"
                        placeholder="Adicione uma descrição para este documento..."
                        rows="2"
                        class="mt-1"
                    />
                </div>

                <Button
                    @click="uploadDocument"
                    :disabled="!selectedFile || isUploading"
                >
                    {{ isUploading ? 'A carregar...' : 'Carregar Documento' }}
                </Button>
            </div>

            <!-- Documents List -->
            <div v-if="documents.length > 0" class="space-y-3">
                <h3 class="text-sm font-medium">Documentos Carregados</h3>

                <div
                    v-for="document in documents"
                    :key="document.id"
                    class="flex items-center justify-between rounded-lg border border-gray-200 p-3 dark:border-gray-700"
                >
                    <div class="flex items-start space-x-3">
                        <span class="text-2xl">{{ getFileIcon(document.mime_type) }}</span>
                        <div>
                            <p class="font-medium">{{ document.name }}</p>
                            <p v-if="document.description" class="text-sm text-gray-500">{{ document.description }}</p>
                            <p class="text-xs text-gray-400">
                                {{ document.size_formatted }} • {{ new Date(document.created_at).toLocaleDateString('pt-PT') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <Button
                            variant="outline"
                            size="sm"
                            @click="downloadDocument(document.download_url)"
                        >
                            Download
                        </Button>
                        <Button
                            variant="destructive"
                            size="sm"
                            @click="deleteDocument(document.id)"
                        >
                            Eliminar
                        </Button>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-8 text-gray-500">
                <p>Nenhum documento carregado</p>
            </div>
        </CardContent>
    </Card>
</template>
