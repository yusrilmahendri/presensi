<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use App\Exports\AttendancesExport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    
    protected static ?string $navigationLabel = 'Data Absensi';
    
    protected static ?string $navigationGroup = 'Absensi';
    
    protected static ?int $navigationSort = 10;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user && $user->isAdmin() && !$user->isSuperAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Karyawan')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('shift_id')
                    ->label('Shift')
                    ->relationship('shift', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('attendance_location_id')
                    ->label('Lokasi')
                    ->relationship('attendanceLocation', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label('Tipe')
                    ->options([
                        'check_in' => 'Masuk',
                        'check_out' => 'Keluar',
                    ])
                    ->required(),
                Forms\Components\DateTimePicker::make('attendance_time')
                    ->label('Waktu Absen')
                    ->required()
                    ->default(now()),
                Forms\Components\TextInput::make('latitude')
                    ->label('Latitude')
                    ->numeric()
                    ->required()
                    ->step(0.00000001),
                Forms\Components\TextInput::make('longitude')
                    ->label('Longitude')
                    ->numeric()
                    ->required()
                    ->step(0.00000001),
                Forms\Components\FileUpload::make('photo')
                    ->label('Foto')
                    ->image()
                    ->directory('attendances')
                    ->visibility('public'),
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('shift.name')
                    ->label('Shift')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'check_in' => 'success',
                        'check_out' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'check_in' => 'Masuk',
                        'check_out' => 'Keluar',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('attendance_time')
                    ->label('Waktu Absen')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('attendanceLocation.name')
                    ->label('Lokasi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Foto')
                    ->circular(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Karyawan')
                    ->relationship('user', 'name'),
                SelectFilter::make('shift_id')
                    ->label('Shift')
                    ->relationship('shift', 'name'),
                SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'check_in' => 'Masuk',
                        'check_out' => 'Keluar',
                    ]),
                Filter::make('attendance_time')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('attendance_time', '>=', $date),
                            )
                            ->when(
                                $data['end_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('attendance_time', '<=', $date),
                            );
                    }),
            ])
            ->headerActions([
                Action::make('export_excel')
                    ->label('Export Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Sampai Tanggal'),
                        Forms\Components\Select::make('user_id')
                            ->label('Karyawan')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('type')
                            ->label('Tipe')
                            ->options([
                                'check_in' => 'Check In',
                                'check_out' => 'Check Out',
                            ]),
                    ])
                    ->action(function (array $data) {
                        $fileName = 'laporan-absensi-' . now()->format('Y-m-d-His') . '.xlsx';
                        return Excel::download(
                            new AttendancesExport(
                                $data['start_date'] ?? null,
                                $data['end_date'] ?? null,
                                $data['user_id'] ?? null,
                                $data['type'] ?? null
                            ),
                            $fileName
                        );
                    }),
                Action::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-text')
                    ->color('danger')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Sampai Tanggal'),
                        Forms\Components\Select::make('user_id')
                            ->label('Karyawan')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('type')
                            ->label('Tipe')
                            ->options([
                                'check_in' => 'Check In',
                                'check_out' => 'Check Out',
                            ]),
                    ])
                    ->action(function (array $data) {
                        $query = Attendance::with(['user', 'shift', 'attendanceLocation'])
                            ->orderBy('attendance_time', 'desc');

                        if (!empty($data['start_date'])) {
                            $query->whereDate('attendance_time', '>=', $data['start_date']);
                        }

                        if (!empty($data['end_date'])) {
                            $query->whereDate('attendance_time', '<=', $data['end_date']);
                        }

                        if (!empty($data['user_id'])) {
                            $query->where('user_id', $data['user_id']);
                        }

                        if (!empty($data['type'])) {
                            $query->where('type', $data['type']);
                        }

                        $attendances = $query->get();

                        $pdf = Pdf::loadView('exports.attendances-pdf', [
                            'attendances' => $attendances,
                            'startDate' => $data['start_date'] ?? null,
                            'endDate' => $data['end_date'] ?? null,
                        ])->setPaper('a4', 'landscape');

                        $fileName = 'laporan-absensi-' . now()->format('Y-m-d-His') . '.pdf';
                        return response()->streamDownload(fn () => print($pdf->output()), $fileName);
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('attendance_time', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'view' => Pages\ViewAttendance::route('/{record}'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}

